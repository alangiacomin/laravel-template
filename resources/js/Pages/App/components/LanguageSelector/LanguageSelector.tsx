import {ChangeEvent, FC, ReactNode} from "react";
import {usePage} from "@inertiajs/react";
import useInertia from "../../../../hooks/useInertia.ts";
import {SharedPageProps} from "../../../page.types.ts";

const LanguageSelector: FC = (): ReactNode => {
    const {locale} = usePage<SharedPageProps>().props;
    const {inertiaRouter} = useInertia();

    const changeLocale = (event: ChangeEvent<HTMLSelectElement>) => {
        inertiaRouter.post(route('language.update'), {
            locale: event.target.value,
        });
    };

    return (
        <label className="d-flex align-items-center gap-2" htmlFor="language-selector">
            <span className="visually-hidden">Language</span>
            <select
                id="language-selector"
                className="form-select form-select-sm"
                value={locale}
                onChange={changeLocale}
                aria-label="Language"
            >
                <option value="it">IT</option>
                <option value="en">EN</option>
                <option value="fr">FR</option>
            </select>
        </label>
    );
};

export default LanguageSelector;
