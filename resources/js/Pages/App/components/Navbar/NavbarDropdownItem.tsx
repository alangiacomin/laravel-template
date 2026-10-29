import { Link } from "@inertiajs/react";
import type { FC, MouseEvent, ReactNode } from "react";
import { NOOP } from "../../../../constants.ts";

type NavbarDropdownItemProps = {
  to: string;
  method?: "get" | "post";
  children: ReactNode;
  onClick?: (event: MouseEvent) => void;
};

const NavbarDropdownItem: FC<NavbarDropdownItemProps> = ({
  to,
  method = "get",
  children,
  onClick = NOOP,
}: NavbarDropdownItemProps): ReactNode => {
  return (
    <li>
      <Link
        className={`dropdown-item`}
        onClick={onClick}
        href={to}
        method={method}
        as={method === "get" ? "a" : "button"}
      >
        {children}
      </Link>
    </li>
  );
};

export default NavbarDropdownItem;
