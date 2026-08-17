import {FC, ReactNode} from "react";

const NavbarDropdownDivider: FC = (): ReactNode => {
    return (
        <li>
            <hr className="dropdown-divider"/>
        </li>);
};

export default NavbarDropdownDivider;
