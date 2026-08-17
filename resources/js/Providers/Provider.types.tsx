import {ReactNode} from "react";
import {UserData} from "../types/generated";

export type ProviderProps = {
    children: ReactNode;
}

export type IMainContext = {
    user: UserData | null;
    setUser: (u: UserData | null) => void;
}
