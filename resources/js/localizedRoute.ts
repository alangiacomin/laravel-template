const supportedLocales = ['it', 'en'] as const;

export const currentLocale = (): (typeof supportedLocales)[number] => {
    const locale = window.location.pathname.split('/').filter(Boolean)[0];

    return supportedLocales.includes(locale as (typeof supportedLocales)[number])
        ? locale as (typeof supportedLocales)[number]
        : 'it';
};

export const localizedRoute = (name: string, parameters?: Record<string, unknown>) =>
    route(`${currentLocale()}.${name}` as never, parameters);
