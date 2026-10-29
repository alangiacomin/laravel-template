import { Link } from "@inertiajs/react";
import type { FC, ReactNode } from "react";
import { NOOP } from "../../../../constants.ts";
import { useRoutes } from "../../../../hooks/useRoutes.ts";

type NavbarItemProps = {
  to: string;
  children: ReactNode;
  onClick?: () => void;
};

const NavbarItem: FC<NavbarItemProps> = ({
  to,
  children,
  onClick = NOOP,
}: NavbarItemProps): ReactNode => {
  const { isActive } = useRoutes();

  return (
    <li className="nav-item">
      <Link
        className={`nav-link text-uppercase fw-bold ${isActive(to) ? "active" : ""}`}
        href={to}
        onClick={onClick}
      >
        {children}
      </Link>
    </li>
  );
};

export default NavbarItem;
