import {usePage} from '@inertiajs/react';
import {SharedPageProps} from "../Pages/page.types.ts";
import {useCallback} from "react";

export const useRoutes = () => {
    const {url} = usePage<SharedPageProps>();

    const normalizePath = useCallback((path: string): string => {
        const segments = path.split('/').filter(Boolean);

        return '/' + segments.join('/');
    }, []);

    const routes = {
        app: {
            home: () => route('home'),
            examplePage: () => route('example.page'),
            login: () => route('login'),
            logout: () => route('logout'),
            register: () => route('register'),
            user: () => route('user.show'),
        },
        admin: {
            dashboard: () => route('admin.dashboard'),
            users: () => route('admin.users'),
            user: (id: number) => route('admin.user.show', {id}),
            roles: () => route('admin.roles'),
            role: (id: number) => route('admin.role.show', {id}),
        },
    }

    const isActive = (to: string): boolean => {
        const currentPath = normalizePath(url);
        const targetPath = normalizePath(
            new URL(to.split('?')[0], window.location.origin).pathname
        );

        // Exact match
        if (currentPath === targetPath) {
            return true;
        }

        // If targetPath is a root ('/' or '/admin'), do not do partial matching
        const roots = [
            normalizePath(routes.app.home().replace(window.location.origin, '')),
            normalizePath(routes.admin.dashboard().replace(window.location.origin, '')),
        ];
        if (roots.includes(targetPath)) {
            return false;
        }

        // Partial match: currentPath starts with targetPath
        return currentPath.startsWith(targetPath + '/');
    };

    return {
        isActive,
        ...routes,
    };
};
