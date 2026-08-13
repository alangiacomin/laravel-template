import MainProvider from "./Providers/MainProvider.tsx";
import ToastStack from "./components/Toast/ToastStack.tsx";
import {ReactNode} from "react";

export const App = ({children}: { children: ReactNode }) =>
    <MainProvider>
        {children}
        <ToastStack/>
    </MainProvider>
