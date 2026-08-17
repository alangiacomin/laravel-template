import useTranslations from "./useTranslations.tsx";

const useErrors = (status: number, reason?: string): { title: string, description: string, hint: string } => {
    const __ = useTranslations();

    const titles: Record<number, string> = {
        403: __('error.403_title'),
        404: __('error.404_title'),
        500: __('error.500_title'),
        503: __('error.503_title'),
    };
    const title = titles[status] || __('error.title');

    console.log(reason === __('error.account_banned'), reason, __('error.account_banned'));

    const descriptions: Record<number, string | ((reason?: string) => string)> = {
        403: (reason) => {
            switch (reason) {
                case __('error.account_banned'):
                    return __('error.403_message_banned');
                default:
                    return __('error.403_message');
            }
        },
        404: __('error.404_message'),
        500: __('error.500_message'),
        503: __('error.503_message'),
    };
    const descriptionEntry = descriptions[status];
    const description =
        typeof descriptionEntry === "function"
            ? descriptionEntry(reason)
            : descriptionEntry ?? __('error.message');

    const hints: Record<number, string | ((reason?: string) => string)> = {
        403: (reason) => {
            switch (reason) {
                case __('error.account_banned'):
                    return __('error.403_hint_banned');
                default:
                    return __('error.403_hint');
            }
        },
    };
    const hintEntry = hints[status];
    const hint =
        typeof hintEntry === "function"
            ? hintEntry(reason)
            : hintEntry ?? __('error.hint');

    return {
        title,
        description,
        hint,
    }
}

export default useErrors;
