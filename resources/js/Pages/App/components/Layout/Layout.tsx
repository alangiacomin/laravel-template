import type { FC, ReactNode } from "react";
import useTranslations from "../../../../hooks/useTranslations.tsx";
import Navbar from "../Navbar/Navbar.tsx";

type LayoutProps = {
  children: ReactNode;
};
const Layout: FC<LayoutProps> = ({ children }: LayoutProps): ReactNode => {
  const __ = useTranslations();
  return (
    <div className="d-flex flex-column min-dvh-100">
      <a href="#main-content" className="visually-hidden-focusable">
        {__("skip_to_content")}
      </a>
      <Navbar />
      <main id="main-content" tabIndex={-1}>
        {children}
      </main>

      <footer className="bg-dark text-white py-3 mt-auto">
        <div className="container text-center">
          <p className="mb-0">
            {__("copyright", { year: new Date().getFullYear() })}
          </p>
        </div>
      </footer>
    </div>
  );
};

export default Layout;
