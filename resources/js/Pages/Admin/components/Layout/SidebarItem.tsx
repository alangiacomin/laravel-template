import { Link } from "@inertiajs/react";
import classNames from "classnames";
import type { FC, ReactNode } from "react";
import { NOOP } from "../../../../constants.ts";
import { useRoutes } from "../../../../hooks/useRoutes.ts";

type SidebarItemProps = {
  to: string;
  children: ReactNode;
  onClick?: () => void;
};
const SidebarItem: FC<SidebarItemProps> = ({
  to,
  children,
  onClick = NOOP,
}: SidebarItemProps): ReactNode => {
  const { isActive } = useRoutes();

  return (
    <li className="nav-item">
      <Link
        className={classNames("nav-link", "text-white", {
          "bg-secondary": isActive(to),
        })}
        href={to}
        onClick={onClick}
      >
        {children}
      </Link>
    </li>
  );
};

export default SidebarItem;
