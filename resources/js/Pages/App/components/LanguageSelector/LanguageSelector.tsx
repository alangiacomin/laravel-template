import {ChangeEvent, FC, ReactNode} from "react";
import {usePage} from "@inertiajs/react";
import useInertia from "../../../../hooks/useInertia.ts";
import useTranslations from "../../../../hooks/useTranslations.tsx";
import {SharedPageProps} from "../../../page.types.ts";
import {localizedRoute} from "../../../../localizedRoute.ts";

const LanguageSelector: FC = (): ReactNode => {
    const {locale, locales} = usePage<SharedPageProps>().props;
    const {inertiaRouter} = useInertia();
    const __ = useTranslations();

    const changeLocale = (event: ChangeEvent<HTMLSelectElement>) => {
        inertiaRouter.post(localizedRoute('language.update'), {
            locale: event.target.value,
        });
    };

    return (
        <label className="d-flex align-items-center gap-2" htmlFor="language-selector">
            <span className="visually-hidden">{__('global.language')}</span>
            <select
                id="language-selector"
                className="form-select form-select-sm"
                value={locale}
                onChange={changeLocale}
                aria-label={__('global.language')}
            >
                {locales.map((availableLocale) => (
                    <option key={availableLocale} value={availableLocale}>
                        {availableLocale.toUpperCase()}
                    </option>
                ))}
            </select>
        </label>
    );
};

export default LanguageSelector;
