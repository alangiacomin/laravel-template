import {FC, ReactNode} from "react";
import {Link} from "@inertiajs/react";
import {NOOP} from "../../../../constants.ts";

type NavbarDropdownItemProps = {
    to: string;
    children: ReactNode;
    onClick?: (event: MouseEvent) => void;
}

const NavbarDropdownItem: FC<NavbarDropdownItemProps> = ({
                                                             to,
                                                             children,
                                                             onClick = NOOP
                                                         }: NavbarDropdownItemProps): ReactNode => {
    return (
        <li>
            <Link
                className={`dropdown-item`}
                onClick={onClick}
                href={to}
            >
                {children}
            </Link>
        </li>);
};

export default NavbarDropdownItem;
