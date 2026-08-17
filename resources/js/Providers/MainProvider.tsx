import {FC, ReactNode} from "react"
import AuthProvider from "./AuthProvider"
import ToastProvider from "./ToastProvider"
import {ProviderProps} from "./Provider.types.tsx";

const MainProvider: FC<ProviderProps> = ({children}): ReactNode => {
    return (
        <AuthProvider>
            <ToastProvider>
                {children}
            </ToastProvider>
        </AuthProvider>
    )
}

export default MainProvider
