import {UserData} from "../types/generated";

export type SharedPageProps = {
    translations: Record<string, Record<string, string>>,
    auth: {
        user: UserData,
        capabilities: string[]
    },
}

export type TranslationPageProps = {
    translations: Record<string, Record<string, string>>
}
