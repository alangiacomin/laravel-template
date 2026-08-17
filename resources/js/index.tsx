import {createRoot} from 'react-dom/client';
import './bootstrap';
import {createInertiaApp} from "@inertiajs/react";
import {NOOP} from "./constants.ts";
import {JSX, ReactNode} from "react";
import AppLayout from "./Pages/App/components/Layout/Layout.tsx";
import AdminLayout from "./Pages/Admin/components/Layout/Layout.tsx";
import {App} from "./App.tsx";

type InertiaPageComponent<P = Record<string, unknown>> = {
    layout?: (page: ReactNode) => JSX.Element;
} & ((props: P) => JSX.Element);

type PageModule<P = Record<string, unknown>> = {
    default: InertiaPageComponent<P>;
};

createInertiaApp({
    id: 'app',
    resolve: <P = Record<string, unknown>>(name: string): InertiaPageComponent<P> => {
        const pages = import.meta.glob('./Pages/**/*.tsx', {eager: true}) as Record<string, PageModule<P>>;
        const pageModule = pages[`./Pages/${name}.tsx`];
        if (!pageModule) {
            throw new Error(`Pagina non trovata: ${name}`);
        }
        const page = pageModule.default;
        page.layout ??= name.startsWith('Admin/')
            ? ((pageContent: ReactNode) => <App><AdminLayout>{pageContent}</AdminLayout></App>)
            : ((pageContent: ReactNode) => <App><AppLayout>{pageContent}</AppLayout></App>);

        return page;
    },
    setup({el, App, props}) {
        createRoot(el).render(<App {...props} />)
    },
    progress: false,
    // defaults: {
    //     visitOptions: (href, options) => {
    //         return {viewTransition: true}
    //     },
    // },
    // defaults: {
    //     future: {
    //         useDialogForErrorModal: true,
    //     },
    // },
    // progress: {
    //     // The delay after which the progress bar will appear, in milliseconds...
    //     delay: 250,
    //     // The color of the progress bar...
    //     color: 'white', //'#29d',
    //     // Whether to include the default NProgress styles...
    //     includeCSS: true,
    //     // Whether the NProgress spinner will be shown...
    //     showSpinner: false,
    // },
    // defaults: {
    //     form: {
    //         recentlySuccessfulDuration: 5000,
    //     },
    //     prefetch: {
    //         cacheFor: "1m",
    //         hoverDelay: 150,
    //     },
    //     visitOptions: (href, options) => {
    //         return {
    //             headers: {
    //                 ...options.headers,
    //                 "X-Custom-Header": "value",
    //             },
    //         };
    //     },
    // },
}).then(NOOP)


// const container: HTMLElement | null = document.getElementById('app');
//
// if (!container) {
//     throw new Error('Failed to find the root element');
// }
//
// const root: Root = createRoot(container);
// root.render(<App/>);
