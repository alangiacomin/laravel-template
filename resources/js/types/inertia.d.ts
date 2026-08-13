import {Page} from "@inertiajs/core";

export interface FlashProps {
    success?: unknown;
    error?: string;
}

export interface InertiaPageProps {
    flash?: FlashProps;
    // aggiungi altre proprietà condivise se vuoi
}

export type InertiaPage = Page<InertiaPageProps>;
