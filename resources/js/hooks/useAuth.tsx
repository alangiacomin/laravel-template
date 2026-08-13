import {usePage} from "@inertiajs/react";
import {SharedPageProps} from "../Pages/page.types.ts";
import {useEffect} from "react";
import {usePrivateChannel} from "./useEcho.ts";
import {UserData} from "../types/generated";

export type AuthUser = UserData & {
    can: (perm: string) => boolean;
};

const useAuth = (): AuthUser | null => {

    const {auth} = usePage<SharedPageProps>().props;
    const {user, capabilities} = auth;

    const {listen} = usePrivateChannel(user?.id);

    useEffect(() => {
        if (!user?.id) return;

        return listen("utente.registrato", () => {
            console.log("--- UTENTE REGISTRATO ---");
        });
    }, [listen, user]);


    return user
        ? {
            ...user,
            can: (capability: string) => {
                return capabilities.includes(capability);
            },
        }
        : null;
};

export default useAuth;
