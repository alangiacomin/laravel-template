import {FC, ReactNode} from "react";
import SidebarItem from "./SidebarItem.tsx";
import useAuth from "../../../../hooks/useAuth.tsx";
import {GateEnum} from "../../../../types/generated";
import {useRoutes} from "../../../../hooks/useRoutes.ts";
import useTranslations from "../../../../hooks/useTranslations.tsx";
import HomeIcon from "../../icons/HomeIcon.tsx";

import classNames from "classnames";
import {NOOP} from "../../../../constants.ts";

type SidebarProps = {
    isOpen: boolean;
    closeSidebar: () => void;
}

const Sidebar: FC<SidebarProps> = ({isOpen, closeSidebar = NOOP}): ReactNode => {

    const __ = useTranslations();
    const routes = useRoutes();
    const user = useAuth();

    return (
        <aside
            className={classNames(
                "flex-grow-1 bg-dark text-white p-3 flex-shrink-0 h-100",
                "position-lg-static",
                {
                    "position-absolute": isOpen,
                    "d-none d-lg-block": !isOpen,
                    "d-block": isOpen
                }
            )}
            style={{width: '240px', minWidth: '240px', maxWidth: '240px', zIndex: 1050}}
        >
            <h5 className="mb-4">{__('admin.admin_panel')}</h5>

            <ul className="nav nav-pills flex-column gap-1">
                {user && user.can(GateEnum.ADMIN_ACCESS) && (
                    <SidebarItem to={routes.admin.dashboard()}
                                 onClick={closeSidebar}>{__('admin.dashboard')}</SidebarItem>
                )}
                {user && user.can(GateEnum.USER_VIEW) && (
                    <SidebarItem to={routes.admin.users()} onClick={closeSidebar}>{__('admin.users')}</SidebarItem>
                )}
                {user && user.can(GateEnum.ROLE_VIEW) && (
                    <SidebarItem to={routes.admin.roles()} onClick={closeSidebar}>{__('admin.roles')}</SidebarItem>
                )}
            </ul>

            <hr className="text-secondary my-3"/>

            <a className="nav-link text-white d-flex align-items-center" href={routes.app.home()}>
                <HomeIcon/> {__('admin.back_to_site')}
            </a>
        </aside>
    );
}

export default Sidebar;
