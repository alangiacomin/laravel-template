import {FC, ReactNode} from "react";
import Page from "../../Admin/components/Page/Page.tsx";
import {Link, usePage} from "@inertiajs/react";
import {AdminUserData, GateEnum} from "../../../types/generated";
import classNames from "classnames";
import useAuth from "../../../hooks/useAuth.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import CheckIcon from "../icons/CheckIcon.tsx";

type UsersPageProps = {
    users: AdminUserData[],
}

const Users: FC = (): ReactNode => {

    const __ = useTranslations();
    const routes = useRoutes();
    const {users} = usePage<UsersPageProps>().props;
    const currentUser = useAuth();

    return (
        <Page
            title={__('admin.user_list')}
            subtitle={__('admin.user_list_subtitle')}
            browserTitle={__('admin.user_list')}
            breadcrumb={[
                {name: __('admin.admin_panel'), href: routes.admin.dashboard()},
                {name: __('admin.users'), href: routes.admin.users()},
            ]}
        >
            <div className="table-responsive">
                <table className="table">
                    <thead>
                    <tr>
                        <th>{__('user.id')}</th>
                        <th>{__('user.name')}</th>
                        <th>{__('user.email')}</th>
                        <th>{__('user.status_verified')}</th>
                        <th>{__('admin.roles')}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody className="table-group-divider">
                    {users && users.map((user: AdminUserData) => (
                        <tr key={user.id}
                            className={classNames([
                                {'fw-lighter': user.isBanned},
                            ])}>
                            <td>{user.id}</td>
                            <td>{user.name}</td>
                            <td>{user.email}</td>
                            <td>
                                {user.isVerified && !user.isBanned && (
                                    <span className={classNames([
                                        "badge",
                                        "rounded-pill",
                                        "bg-success",
                                    ])}>
                                        <CheckIcon/>
                                    </span>
                                )}
                                {user.isVerified && user.isBanned && (
                                    <CheckIcon/>
                                )}
                            </td>
                            <td>
                                {user.roles.map((role: string) => (
                                    <span key={role}
                                          className={classNames([
                                              "badge",
                                              "rounded-pill",
                                              "mx-1",
                                              "text-bg-info",
                                              {'fw-lighter': user.isBanned},
                                          ])}>
                                        {role}
                                    </span>
                                ))}
                            </td>
                            <td>
                                <div className="d-flex gap-2 justify-content-end">
                                    {currentUser && (
                                        currentUser.can(GateEnum.USER_EDIT)
                                            ? (
                                                <Link
                                                    className={classNames(
                                                        "btn btn-sm btn-outline-primary",
                                                    )}
                                                    href={routes.admin.user(user.id)}
                                                >
                                                    {__('edit')}
                                                </Link>)
                                            : (
                                                <Link
                                                    className={classNames(
                                                        "btn btn-sm btn-outline-primary",
                                                    )}
                                                    href={routes.admin.user(user.id)}
                                                >
                                                    {__('view')}
                                                </Link>
                                            )
                                    )}
                                    {currentUser && currentUser.can(GateEnum.USER_MANAGE) && (
                                        <Link
                                            className={classNames(
                                                "btn btn-sm btn-outline-danger",
                                            )}
                                            href={'#'}
                                        >
                                            {__('delete')}
                                        </Link>
                                    )}
                                </div>
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            </div>
        </Page>
    );
}

export default Users;
