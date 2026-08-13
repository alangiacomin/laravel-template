/* disable eslint any because this type is required by inertia */
/* eslint-disable @typescript-eslint/no-explicit-any */

import {InertiaFormProps} from "@inertiajs/react";
import {createContext, ReactNode, SubmitEvent, useContext} from "react";

type FormProps<T extends Record<string, any>> = {
    form: InertiaFormProps<T>;
    children: ReactNode;
}

const FormContext = createContext<unknown | null>(null);

const useFormContext = <T extends Record<string, any>>() => {
    const ctx = useContext(FormContext);
    if (!ctx) throw new Error("useFormContext must be used within a <Form>");
    return ctx as InertiaFormProps<T>;
};

const Form = <T extends Record<string, any>>({form, children}: FormProps<T>) => {

    const handleSubmit = async (e: SubmitEvent<HTMLFormElement>) => {
        e.preventDefault();
    };

    return (
        <FormContext.Provider value={form}>
            <form onSubmit={handleSubmit}>
                {children}
            </form>
        </FormContext.Provider>
    );
}

export default Form;
export {
    // eslint-disable-next-line react-refresh/only-export-components
    useFormContext,
};
