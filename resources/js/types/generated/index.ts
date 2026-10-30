export type AdminUserData = {
  readonly id: number;
  readonly name: string;
  readonly email: string;
  readonly isVerified: boolean;
  readonly isBanned: boolean;
  readonly avatar: string;
  readonly created_at: string | null;
  readonly roles: string[];
};
export type CursorPaginatedDataCollection<TKey, TValue> = CursorPaginator<
  TKey,
  TValue
>;
export type CursorPaginator<TKey, TValue> = {
  data: TKey extends string ? Record<TKey, TValue> : TValue[];
  links: {
    url: string | null;
    label: string;
    active: boolean;
  }[];
  meta: {
    path: string;
    per_page: number;
    next_cursor: string | null;
    next_page_url: string | null;
    prev_cursor: string | null;
    prev_page_url: string | null;
  };
};
export type CursorPaginatorInterface<TKey, TValue> = CursorPaginator<
  TKey,
  TValue
>;
export type DashboardStats = {
  users_total_count: number;
  users_total_count_unverified: number;
  users_month_count: number;
  users_month_count_unverified: number;
  users_last_month_count: number;
  users_last_month_count_unverified: number;
};
export type EditUserRequest = {
  name: string;
};
export type ForgotPasswordRequest = {
  email: string;
};
export enum GateEnum {
  USER_VIEW = "user_view",
  USER_EDIT = "user_edit",
  USER_MANAGE = "user_manage",
  ROLE_VIEW = "role_view",
  ROLE_EDIT = "role_edit",
  ROLE_MANAGE = "role_manage",
  ADMIN_ACCESS = "admin_access",
}
export type LengthAwarePaginator<TKey, TValue> = {
  data: TKey extends string ? Record<TKey, TValue> : TValue[];
  links: {
    url: string | null;
    label: string;
    active: boolean;
  }[];
  meta: {
    total: number;
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
  };
};
export type LengthAwarePaginatorInterface<TKey, TValue> = LengthAwarePaginator<
  TKey,
  TValue
>;
export type LoginRequest = {
  email: string;
  password: string;
  remember: boolean;
};
export type PaginatedDataCollection<TKey, TValue> = LengthAwarePaginator<
  TKey,
  TValue
>;
export enum PermissionEnum {
  USER_CREATE = "user_create",
  USER_READ = "user_read",
  USER_UPDATE = "user_update",
  USER_DELETE = "user_delete",
  ROLE_CREATE = "role_create",
  ROLE_READ = "role_read",
  ROLE_UPDATE = "role_update",
  ROLE_DELETE = "role_delete",
}
export type RegisterRequest = {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
};
export type ResetPasswordRequest = {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
};
export type Role = {
  id: number;
  name: string;
  permissions: string[];
};
export enum RoleEnum {
  SUPER_ADMIN = "super-admin",
  ADMIN = "admin",
  EDITOR = "editor",
  USER = "user",
}
export type UserData = {
  readonly id: number;
  readonly name: string;
  readonly email: string;
  readonly isVerified: boolean;
  readonly isBanned: boolean;
  readonly avatar: string;
  readonly created_at: string | null;
};
