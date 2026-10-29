import type { FC, ReactNode } from "react";
import AuthProvider from "./AuthProvider";
import type { ProviderProps } from "./Provider.types.tsx";
import ToastProvider from "./ToastProvider";

const MainProvider: FC<ProviderProps> = ({ children }): ReactNode => {
  return (
    <AuthProvider>
      <ToastProvider>{children}</ToastProvider>
    </AuthProvider>
  );
};

export default MainProvider;
