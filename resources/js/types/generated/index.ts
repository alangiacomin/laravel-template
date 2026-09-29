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
export type AlboDetailData = {
  id: number;
  titolo: string;
  testata: TestataNameData;
  pubblicazioni: Array<AlboDetailPubblicazioneData>;
};
export type AlboDetailPubblicazioneData = {
  serie_id: number;
  serie: string;
  id: number;
  numero: number;
  numero_gruppo: number | null;
  data_pubblicazione: string | null;
};
export type AlboInputData = {
  titolo: string;
  testata_id: string | number;
  pubblicazioni: Array<AlboPubblicazioneInputData>;
};
export type AlboOptionData = {
  id: number;
  titolo: string;
};
export type AlboPubblicazioneData = {
  id: number;
  numero: number;
  numero_gruppo: number | null;
  data_pubblicazione: string | null;
};
export type AlboPubblicazioneInputData = {
  serie_id: number;
  numero: number;
  numero_gruppo: number | null;
  data_pubblicazione: string | null;
};
export type AlboSummaryData = {
  id: number;
  titolo: string;
  testata: TestataOptionData;
  pubblicazioni: Array<AlboSummaryPubblicazioneData>;
};
export type AlboSummaryPubblicazioneData = {
  serie_id: number;
  serie: string;
  numero: number;
  numero_gruppo: number | null;
  data_pubblicazione: string | null;
};
export type CatalogComicData = {
  id: number;
  titolo: string;
  testata: CatalogOptionData;
  pubblicazioni: Array<CatalogComicPublicationData>;
};
export type CatalogComicPublicationData = {
  serie: CatalogSerieOptionData;
  numero: number;
  numeroGruppo: number | null;
  dataPubblicazione: string | null;
};
export type CatalogOptionData = {
  id: number;
  titolo: string;
};
export type CatalogSerieOptionData = {
  id: number;
  titolo: string;
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
export type SerieDetailData = {
  id: number;
  titolo: string;
  albi: Array<AlboOptionData>;
};
export type SerieInputData = {
  titolo: string;
};
export type SerieOptionData = {
  id: number;
  titolo: string;
};
export type SerieSummaryData = {
  id: number;
  titolo: string;
  pubblicazioni_count: number;
};
export type TestataDetailData = {
  id: number;
  titolo: string;
  albi: Array<AlboOptionData>;
};
export type TestataInputData = {
  titolo: string;
};
export type TestataNameData = {
  titolo: string;
};
export type TestataOptionData = {
  id: number;
  titolo: string;
};
export type TestataSummaryData = {
  id: number;
  titolo: string;
  albi_count: number;
  albi: Array<AlboOptionData>;
};
export type UserData = {
  id: number;
  name: string;
  email: string;
  isVerified: boolean;
  isBanned: boolean;
  avatar: string;
  created_at: string | null;
};
