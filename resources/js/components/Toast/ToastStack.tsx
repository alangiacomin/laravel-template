import {useContext} from "react"
import {ToastContext} from "../../Providers/ToastContext.tsx";

const ToastStack = () => {
    const toastContext = useContext(ToastContext);

    if (!toastContext) {
        return null;
    }

    const {toasts, removeToast} = toastContext;

    if (!toasts.length) {
        return null;
    }

    return (
        <div className="toast-container position-fixed top-0 end-0 p-3"
             style={{zIndex: 1055}}>
            {toasts.map(toast => (
                <div key={toast.id} className={`toast show text-bg-${toast.type} mb-2`}>
                    <div className="toast-body d-flex justify-content-between align-items-center">
                        <span>{toast.message}</span>
                        <button
                            className="btn-close btn-close-white ms-2"
                            onClick={() => removeToast(toast.id)}
                        />
                    </div>
                </div>
            ))}
        </div>
    )
}

export default ToastStack
