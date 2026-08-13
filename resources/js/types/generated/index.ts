export type AdminUserData = {
    id: number;
    name: string;
    email: string;
    isVerified: boolean;
    isBanned: boolean;
    avatar: string;
    created_at: string | null;
    roles: Array<any>;
};
export type EditUserRequest = {
    name: string;
};
export enum GateEnum {
    USER_VIEW = "user_view",
    USER_EDIT = "user_edit",
    USER_MANAGE = "user_manage",
    ROLE_VIEW = "role_view",
    ROLE_EDIT = "role_edit",
    ROLE_MANAGE = "role_manage",
    TODOS_VIEW = "todos_view",
    TODOS_EDIT = "todos_edit",
    TODOS_COMPLETE = "todos_complete",
    TODOS_ASSIGN = "todos_assign",
    TODOS_MANAGE = "todos_manage",
    ADMIN_ACCESS = "admin_access",
}
export type LoginRequest = {
    email: string;
    password: string;
    remember: boolean;
};
export enum PermissionEnum {
    USER_CREATE = "user_create",
    USER_READ = "user_read",
    USER_UPDATE = "user_update",
    USER_DELETE = "user_delete",
    TODOS_CREATE = "todos_create",
    TODOS_READ = "todos_read",
    TODOS_UPDATE = "todos_update",
    TODOS_DELETE = "todos_delete",
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
export type Role = {
    id: number;
    name: string;
    permissions: Array<string>;
};
export enum RoleEnum {
    SUPER_ADMIN = "super-admin",
    ADMIN = "admin",
    EDITOR = "editor",
    USER = "user",
}
export type UserData = {
    id: number;
    name: string;
    email: string;
    isVerified: boolean;
    isBanned: boolean;
    avatar: string;
    created_at: string | null;
};
