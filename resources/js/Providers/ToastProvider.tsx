import {FC, useCallback, useRef, useState} from "react"
import {ProviderProps} from "./Provider.types.tsx"
import {ToastContext} from "./ToastContext.tsx";

export type ToastType = "success" | "danger" | "warning" | "info";

export interface ToastItem {
    id: string,
    message: string,
    type: ToastType,
}

export interface IToastContext {
    pushToast: (message: string, type?: ToastType) => void,
    removeToast: (id: string) => void,
    toasts: ToastItem[],
}

const ToastProvider: FC<ProviderProps> = ({children}) => {
    const [toasts, setToasts] = useState<ToastItem[]>([]);
    const timers = useRef<Record<string, number>>({});

    const removeToast = useCallback((id: string) => {
        clearTimeout(timers.current[id]);
        delete timers.current[id];
        setToasts(prev => prev.filter(t => t.id !== id));
    }, []);

    const pushToast = useCallback((message: string, type: ToastType = "danger") => {
        const id = crypto.randomUUID();

        setToasts(prev => [...prev, {id, message, type}]);

        timers.current[id] = setTimeout(() => {
            removeToast(id);
        }, 2000);
    }, [removeToast]);

    return (
        <ToastContext value={{toasts, pushToast, removeToast}}>
            {children}
        </ToastContext>
    )
}

export default ToastProvider
