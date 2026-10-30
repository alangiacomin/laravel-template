import { usePage } from "@inertiajs/react";
import type { SharedPageProps } from "../Pages/page.types.ts";
import type { UserData } from "../types/generated";

export type AuthUser = UserData & {
  can: (perm: string) => boolean;
};

const useAuth = (): AuthUser | null => {
  const { auth } = usePage<SharedPageProps>().props;
  const { user, capabilities } = auth;

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
