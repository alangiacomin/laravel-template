import {UserData} from "../types/generated";

export type SharedPageProps = {
    locale: string,
    defaultLocale: string,
    routerSlugs: Record<string, Record<string, string>>,
    auth: {
        user: UserData,
        capabilities: string[]
    },
}

export type TranslationPageProps = {
    translations: Record<string, Record<string, string>>
}
