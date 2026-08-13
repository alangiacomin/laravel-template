import {createContext, FC, ReactNode, useEffect, useState} from 'react';
import {ProviderProps} from "./Provider.types.tsx";
import {UserData} from "../types/generated";

//import {AuthApi} from "./api";

export interface IAuthContext {
    user: UserData | null
    setUser: (user: UserData | null) => void
}

const AuthContext = createContext<IAuthContext | undefined>(undefined);

const AuthProvider: FC<ProviderProps> = ({children}: ProviderProps): ReactNode => {
    // const [ready, setReady] = useState<boolean>(true);
    const [user, setUser] = useState<UserData | null>(null);

    // const buildUser = (raw: Partial<Omit<IUser, 'can'>>): IUser | null => {
    //     if (!raw) {
    //         return null;
    //     }
    //
    //     return ({
    //         id: raw.id ?? 0,
    //         email: raw.email ?? '',
    //         name: raw.name ?? '',
    //         isVerified: raw.isVerified ?? false,
    //         isSuperAdmin: raw.isSuperAdmin ?? false,
    //         permissions: raw.permissions ?? [],
    //         can: (perm: string) => (raw.isSuperAdmin ?? false) || (raw.permissions ?? []).includes(perm),
    //     });
    // };

    // const setAndBuildUser = useCallback((u: IUser | null) => {
    //     setUser(!u ? u : buildUser(u as IUser));
    // }, []);

    // Retrieve all cached info from storage, useful to have the app "ready" as soon as the user arrives
    // This is useful if the backend does not respond, so offline data can still be used
    // At the moment only the user is cached, but this can be extended in the future
    // useEffect(() => {
    //     const userCached = sessionStorage.getItem('auth_user');
    //     if (userCached) {
    //         // setUser(JSON.parse(userCached) as IUser);
    //         const parsed = JSON.parse(userCached);
    //         setUser(buildUser(parsed));
    //         console.log("user cached:", parsed);
    //     }
    // }, []);

    // Update from backend with the actual authenticated user
    // useEffect(() => {
    //     // AuthApi.authenticated()
    //     //     .then((res: Partial<IUser>) => {
    //     //         //setUser(res as IUser);
    //     //         setUser(buildUser(res));
    //     //         // console.log("user authenticated:", res);
    //     //     })
    //     //     .finally(() => setReady(true));
    // }, []);

    // Save updated info in sessionStorage
    useEffect(() => {
        if (user) {
            sessionStorage.setItem('auth_user', JSON.stringify(user));
            // console.log("USER", user);
        } else {
            sessionStorage.removeItem('auth_user');
            // console.log("USER");
        }
    }, [user]);

    return (
        <AuthContext.Provider value={{user, setUser}}>
            {children}
        </AuthContext.Provider>
    );
};

export default AuthProvider;
export {AuthContext};
