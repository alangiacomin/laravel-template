import {usePage} from '@inertiajs/react';
import {SharedPageProps} from "../Pages/page.types.ts";

type TranslationPageProps = SharedPageProps & {
    translations?: Record<string, Record<string, string>>;
};

/**
 * Hook per accedere alle traduzioni passate da Laravel via Inertia
 */
const useTranslations = (): (key: string, replacements?: Record<string, string | number>) => string => {
    const {translations} = usePage<TranslationPageProps>().props;

    return (key: string, replacements?: Record<string, string | number>): string => {
        let [section, tKey] = key.split('.');
        if (!tKey) {
            tKey = section;
            section = 'global';
        }
        const notFoundKey = `[${section}.${tKey}]`;

        if (!translations || !(section in translations)) {
            return notFoundKey;
        }

        let translation = translations[section][tKey] ?? notFoundKey;

        if (replacements) {
            Object.entries(replacements).forEach(([placeholder, value]) => {
                translation = translation.replace(`:${placeholder}`, String(value));
            });
        }

        return translation;
    };
}

export default useTranslations;
