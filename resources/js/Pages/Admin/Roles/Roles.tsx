import {FC, ReactNode} from "react";
import Page from "../../Admin/components/Page/Page.tsx";
import {Link, usePage} from "@inertiajs/react";
import classNames from "classnames";
import useAuth from "../../../hooks/useAuth.tsx";
import {GateEnum, Role as RoleType} from "../../../types/generated";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";

type RolesPageProps = {
    roles: RoleType[]
}

const Roles: FC = (): ReactNode => {

    const __ = useTranslations();
    const routes = useRoutes();
    const user = useAuth();
    const {roles} = usePage<RolesPageProps>().props;

    return (
        <Page
            title={__('admin.roles_list')}
            subtitle={__('admin.roles_list_subtitle')}
            browserTitle={__('admin.role_list')}
            breadcrumb={[
                {name: __('admin.admin_panel'), href: routes.admin.dashboard()},
                {name: __('admin.roles'), href: routes.admin.roles()},
            ]}
        >
            <table className="table">
                <thead>
                <tr>
                    <th>{__('user.role')}</th>
                    <th>{__('admin.permissions')}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody className="table-group-divider">
                {roles && roles.map((role) => (
                    <tr key={role.id}>
                        <td>{role.name}</td>
                        <td>
                            {role.permissions.map((perm: string) => (
                                <span key={perm}
                                      className={classNames([
                                          "badge",
                                          "rounded-pill",
                                          "mx-1",
                                          {
                                              "text-bg-danger":
                                                  perm.endsWith('_create')
                                                  || perm.endsWith('_delete')
                                          },
                                          {"text-bg-warning": perm.endsWith('_update')},
                                          {"text-bg-primary": perm.endsWith('_read')},
                                          {
                                              "text-bg-secondary":
                                                  !perm.endsWith('_create')
                                                  && !perm.endsWith('_read')
                                                  && !perm.endsWith('_update')
                                                  && !perm.endsWith('_delete')
                                          },
                                      ])}>
                                    {perm}
                                </span>
                            ))}
                        </td>
                        <td>
                            <div className="d-flex gap-2 justify-content-end">
                                {user && user.can(GateEnum.ROLE_EDIT) && (
                                    <Link className="btn btn-sm btn-outline-primary"
                                          href={routes.admin.role(role.id)}
                                    >
                                        {__('edit')}
                                    </Link>
                                )}
                            </div>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        </Page>
    );
}

export default Roles;
