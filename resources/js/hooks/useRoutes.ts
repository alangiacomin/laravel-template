import {router, usePage} from '@inertiajs/react';
import {SharedPageProps} from "../Pages/page.types.ts";
import {useCallback} from "react";

export const useRoutes = () => {
    const {url, props} = usePage<SharedPageProps>();
    const {locale, defaultLocale, routerSlugs} = props;

    const normalizzaPath = useCallback((path: string): string => {
        const LOCALES = ['it', 'en'];
        const segments = path.split('/').filter(Boolean);

        if (segments.length && LOCALES.includes(segments[0])) {
            segments.shift();
        }

        return '/' + segments.join('/');
    }, []);

    const routes = {
        app: {
            home: () => localeRoute('home'),
            examplePage: () => localeRoute('example.page'),
            login: () => localeRoute('login'),
            logout: () => localeRoute('logout'),
            register: () => localeRoute('register'),
            user: () => localeRoute('user.show'),
        },
        admin: {
            dashboard: () => localeRoute('admin.dashboard'),
            users: () => localeRoute('admin.users'),
            user: (id: number) => localeRoute('admin.user.show', {id}),
            roles: () => localeRoute('admin.roles'),
            role: (id: number) => localeRoute('admin.role.show', {id}),
        },
    }

    const isActive = (to: string): boolean => {
        const currentPath = normalizzaPath(url);
        const targetPath = normalizzaPath(
            new URL(to.split('?')[0], window.location.origin).pathname
        );

        // Exact match
        if (currentPath === targetPath) {
            return true;
        }

        // If targetPath is a root ('/' or '/admin'), do not do partial matching
        const roots = [
            normalizzaPath(routes.app.home().replace(window.location.origin, '')),
            normalizzaPath(routes.admin.dashboard().replace(window.location.origin, '')),
        ];
        if (roots.includes(targetPath)) {
            return false;
        }

        // Partial match: currentPath starts with targetPath
        return currentPath.startsWith(targetPath + '/');
    };

    const localeRoute = (
        routeKey: string,
        params: Record<string, unknown> = {}
    ): string => {

        const slugLocale = locale || defaultLocale;

        if (route().has(routeKey)) {
            if (route().t.routes[routeKey].parameters && route().t.routes[routeKey].parameters.length > 0) {
                route().t.routes[routeKey].parameters.forEach((param: string) => {
                    if (routerSlugs?.[param]?.[slugLocale]) {
                        params[param] = routerSlugs[param][slugLocale];
                    }
                });
            }
        }

        return locale
            ? route(`localized.${routeKey}`, {locale, ...params})
            : route(routeKey, params);
    };

    const changeLocale = (lang: string | null): void => {
        const slugLocale = lang || defaultLocale;

        const currentRoute = route().current();
        const routeKey = currentRoute.replace(/^localized\./, '');
        const currentParams = {...route().params};
        delete currentParams.locale;

        if (routerSlugs?.[routeKey]?.[slugLocale]) {
            currentParams[routeKey] = routerSlugs[routeKey][slugLocale]
        }

        const targetRoute = lang
            ? `localized.${routeKey}`
            : routeKey;

        const url = route(targetRoute, {
            ...currentParams,
            ...(lang ? {locale: lang} : {}),
        });

        router.visit(url, {
            preserveScroll: true,
            preserveState: true,
        });
    }

    // expose route helpers with the correct locale handling
    return {
        isActive,
        changeLocale,
        localeRoute,
        ...routes,
    };
};
