import { createContext } from "react";
import type { IToastContext } from "./ToastProvider.tsx";

export const ToastContext = createContext<IToastContext | undefined>(undefined);
