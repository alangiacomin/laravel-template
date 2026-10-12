import type { ReactNode } from "react";
import ToastStack from "./components/Toast/ToastStack.tsx";
import MainProvider from "./Providers/MainProvider.tsx";

export const App = ({ children }: { children: ReactNode }) => (
  <MainProvider>
    {children}
    <ToastStack />
  </MainProvider>
);
