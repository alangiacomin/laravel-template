import {FC, ReactNode} from "react";
import useAuth from "../../../../hooks/useAuth.tsx";
import useTranslations from "../../../../hooks/useTranslations.tsx";
import {useRoutes} from "../../../../hooks/useRoutes.ts";

type HeaderProps = {
    toggleSidebar: () => void;
    isSidebarOpen: boolean;
}

const Header: FC<HeaderProps> = ({toggleSidebar}): ReactNode => {

    const user = useAuth();
    const routes = useRoutes();
    const __ = useTranslations();

    return (
        <header
            className="bg-dark border-bottom border-secondary px-3 py-2 d-flex align-items-center">
            <button
                className="btn btn-outline-light d-lg-none me-auto"
                onClick={toggleSidebar}
                aria-label="Toggle navigation"
            >
                <i className="bi bi-list"></i>
            </button>
            {!user
                ? (<span>&nbsp;</span>)
                : (
                    <div className="ms-auto d-flex align-items-center gap-3 text-white">
                        <strong className="d-none d-sm-inline">{user.name}</strong>
                        <a href={routes.app.logout()} className="btn btn-sm btn-outline-light">
                            {__('logout')}
                        </a>
                    </div>
                )}
        </header>
    );
}

export default Header;
