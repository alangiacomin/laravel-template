import {createContext} from "react";
import {IToastContext} from "./ToastProvider.tsx";

export const ToastContext = createContext<IToastContext | undefined>(undefined);
