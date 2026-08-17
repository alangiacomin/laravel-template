import {useToast} from "../components/Toast/useToast.tsx";
import {Page, UseFormSubmitOptions, VisitHelperOptions} from "@inertiajs/core";
import {InertiaPage} from "../types/inertia";
import {router} from "@inertiajs/react";

export type ParamValue = string | number | boolean | null | undefined;

export type ParamsType = Record<string, ParamValue>;

export type DataType = ParamsType | FormData;

export type OptionsType = UseFormSubmitOptions | VisitHelperOptions<FormData | Partial<ParamsType>>;

export type PageWithFlash<T> = Page & {
    props: {
        flash?: {
            success?: T;
        };
    };
};

const useInertia = () => {
    const toast = useToast();

    /**
     * Opzioni di default per tutte le chiamate.
     */
    const defaultOptions = {
        preserveState: true,
        preserveScroll: true,
    };

    const cleanedData = (data?: DataType) => {
        return data instanceof FormData ? data : data ? cleanParams(data) : undefined;
    }

    const mergedOptions = (options?: OptionsType) => {
        return {
            ...defaultOptions,
            ...options,
            ...forcedOptions(options),
        }
    };

    /**
     * Rimuove chiavi con valori undefined, null o stringa vuota.
     */
    const cleanParams = <T extends ParamsType>(params: T): Partial<T> => {
        const cleaned: Partial<T> = {};

        for (const key in params) {
            const value = params[key];
            if (value !== undefined && value !== null && value !== "") {
                cleaned[key] = value;
            }
        }

        return cleaned;
    };

    const handleSuccess = (page: InertiaPage, successCallback?: (flash: unknown) => void) => {
        const {error} = page.props.flash ?? {};
        if (error) {
            toast.error(error);
        } else {
            successCallback?.(page);
        }
    };

    // noinspection JSUnusedGlobalSymbols
    const forcedOptions = (options?: OptionsType) => ({
        // only: options?.only ? [...options.only, 'flash'] : [],
        onSuccess: (page: Page) => {
            handleSuccess(page, options?.onSuccess as (flash: unknown) => void);
        },
    });

    const getSuccessData = <T>(pagina: PageWithFlash<T>): T => {
        return pagina.props.flash?.success ?? {} as T;
    };

    return {
        forcedOptions,
        getSuccessData,
        inertiaRouter: {
            get(url: string, params?: ParamsType, options?: OptionsType) {
                const cleaned = params ? cleanParams(params) : undefined;

                return router.get(url, cleaned, mergedOptions(options));
            },

            post(url: string, data?: DataType, options?: OptionsType) {
                return router.post(url, cleanedData(data), mergedOptions(options));
            },

            put(url: string, data?: DataType, options?: OptionsType) {
                return router.put(url, cleanedData(data), mergedOptions(options));
            },

            patch(url: string, data?: DataType, options?: OptionsType) {
                return router.patch(url, cleanedData(data), mergedOptions(options));
            },

            delete(url: string, options?: OptionsType) {
                return router.delete(url, mergedOptions(options));
            },
        }
    }
};

export default useInertia;
