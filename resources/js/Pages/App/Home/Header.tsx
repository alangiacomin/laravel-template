import { Link } from "@inertiajs/react";
import useAuth from "../../../hooks/useAuth.tsx";
import { useRoutes } from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";

const Header = () => {
  const __ = useTranslations();
  const user = useAuth();
  const routes = useRoutes();

  return (
    <header className="hero-section text-center">
      <div className="container">
        <h1 className="display-3 fw-bold mb-4">{__("home.head_title")}</h1>
        <p className="lead mb-5">
          {__("home.head_subtitle")}
          <br />
          {__("home.head_subtitle2")}
        </p>
        <div className="d-grid gap-3 d-sm-flex justify-content-sm-center">
          <Link
            className="btn btn-primary btn-lg px-4 gap-3"
            href={routes.app.examplePage()}
          >
            {user ? __("home.vai_esempio") : __("home.inizia_ora")}
          </Link>
        </div>
      </div>
    </header>
  );
};

export default Header;
