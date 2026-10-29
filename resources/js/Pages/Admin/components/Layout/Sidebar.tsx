import classNames from "classnames";
import { type FC, type ReactNode, useEffect, useState } from "react";
import { NOOP } from "../../../../constants.ts";
import useAuth from "../../../../hooks/useAuth.tsx";
import { useRoutes } from "../../../../hooks/useRoutes.ts";
import useTranslations from "../../../../hooks/useTranslations.tsx";
import { GateEnum } from "../../../../types/generated";
import HomeIcon from "../../icons/HomeIcon.tsx";
import SidebarItem from "./SidebarItem.tsx";

type SidebarProps = {
  isOpen: boolean;
  closeSidebar: () => void;
};

const Sidebar: FC<SidebarProps> = ({
  isOpen,
  closeSidebar = NOOP,
}): ReactNode => {
  const __ = useTranslations();
  const routes = useRoutes();
  const user = useAuth();
  const [isUserManagementOpen, setIsUserManagementOpen] = useState(false);
  const userManagementActive =
    routes.isActive(routes.admin.users()) ||
    routes.isActive(routes.admin.roles());

  useEffect(() => {
    if (userManagementActive) {
      setIsUserManagementOpen(true);
    }
  }, [userManagementActive]);

  const toggleGroup = (
    label: ReactNode,
    isOpen: boolean,
    setIsOpen: (value: boolean) => void,
  ) => (
    <button
      type="button"
      className="nav-link text-secondary text-uppercase small fw-semibold w-100 text-start border-0 bg-transparent px-3"
      aria-expanded={isOpen}
      onClick={() => setIsOpen(!isOpen)}
    >
      <span className="me-2" aria-hidden="true">
        {isOpen ? "v" : ">"}
      </span>
      {label}
    </button>
  );

  return (
    <aside
      className={classNames(
        "flex-grow-1 bg-dark text-white p-3 flex-shrink-0 h-100",
        "position-lg-static",
        {
          "position-absolute": isOpen,
          "d-none d-lg-block": !isOpen,
          "d-block": isOpen,
        },
      )}
      style={{
        width: "240px",
        minWidth: "240px",
        maxWidth: "240px",
        zIndex: 1050,
      }}
    >
      <h5 className="mb-4">{__("admin.admin_panel")}</h5>

      <ul className="nav nav-pills flex-column gap-1">
        {user?.can(GateEnum.ADMIN_ACCESS) && (
          <SidebarItem to={routes.admin.dashboard()} onClick={closeSidebar}>
            {__("admin.dashboard")}
          </SidebarItem>
        )}
        {(user?.can(GateEnum.USER_VIEW) || user?.can(GateEnum.ROLE_VIEW)) && (
          <>
            <li className="nav-item mt-3 mb-1">
              {toggleGroup(
                __("admin.user_management"),
                isUserManagementOpen,
                setIsUserManagementOpen,
              )}
            </li>
            {isUserManagementOpen && (
              <>
                {user?.can(GateEnum.USER_VIEW) && (
                  <SidebarItem to={routes.admin.users()} onClick={closeSidebar}>
                    {__("admin.users")}
                  </SidebarItem>
                )}
                {user?.can(GateEnum.ROLE_VIEW) && (
                  <SidebarItem to={routes.admin.roles()} onClick={closeSidebar}>
                    {__("admin.roles")}
                  </SidebarItem>
                )}
              </>
            )}
          </>
        )}
      </ul>

      <hr className="text-secondary my-3" />

      <a
        className="nav-link text-white d-flex align-items-center"
        href={routes.app.home()}
      >
        <HomeIcon /> {__("admin.back_to_site")}
      </a>
    </aside>
  );
};

export default Sidebar;
