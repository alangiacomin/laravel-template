import { Link } from "@inertiajs/react";
import type { FC, ReactNode } from "react";
import useAuth from "../../../../hooks/useAuth.tsx";
import { useRoutes } from "../../../../hooks/useRoutes.ts";
import useTranslations from "../../../../hooks/useTranslations.tsx";

type HeaderProps = {
  toggleSidebar: () => void;
  isSidebarOpen: boolean;
};

const Header: FC<HeaderProps> = ({ toggleSidebar }): ReactNode => {
  const user = useAuth();
  const routes = useRoutes();
  const __ = useTranslations();

  return (
    <header className="bg-dark border-bottom border-secondary px-3 py-2 d-flex align-items-center">
      <button
        type="button"
        className="btn btn-outline-light d-lg-none me-auto"
        onClick={toggleSidebar}
        aria-label={__("global.toggle_navigation")}
      >
        <i className="bi bi-list"></i>
      </button>
      {!user ? (
        <span>&nbsp;</span>
      ) : (
        <div className="ms-auto d-flex align-items-center gap-3 text-white">
          <strong className="d-none d-sm-inline">{user.name}</strong>
          <Link
            href={routes.app.logout()}
            method="post"
            as="button"
            className="btn btn-sm btn-outline-light"
          >
            {__("logout")}
          </Link>
        </div>
      )}
    </header>
  );
};

export default Header;
