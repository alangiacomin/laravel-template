import {routeFn} from 'ziggy-js';

declare module '*.css' {
    const content: { [className: string]: string };
    export default content;
}

declare global {
    const route: typeof routeFn;
}


interface ImportMeta {
    readonly glob: typeof import('vite/client')['import.meta']['glob'];
}
