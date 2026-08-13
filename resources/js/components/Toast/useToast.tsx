import {useContext} from "react"
import {ToastContext} from "../../Providers/ToastContext.tsx";

export const useToast = () => {
    const context = useContext(ToastContext);

    if (!context) {
        throw new Error("useToast must be used within ToastProvider")
    }

    return {
        success: (message: string) => context.pushToast(message, "success"),
        error: (message: string) => context.pushToast(message, "danger"),
        warning: (message: string) => context.pushToast(message, "warning"),
        info: (message: string) => context.pushToast(message, "info"),
    }
}
