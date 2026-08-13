import {FC, ReactNode, useEffect, useRef, useState} from "react";
import classNames from "classnames";
import {Link} from "@inertiajs/react";

type NavbarDropdownProps = {
    label: ReactNode;
    className?: string;
    children: ReactNode;
}

const NavbarDropdown: FC<NavbarDropdownProps> = ({label, className, children}: NavbarDropdownProps): ReactNode => {
    const [isDropOpen, setIsDropOpen] = useState(false);
    const dropRef = useRef<HTMLLIElement | null>(null);

    const toggleDrop = (e: any) => {
        e.stopPropagation();
        setIsDropOpen(!isDropOpen);
    };

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (dropRef.current && !dropRef.current.contains(event.target as Node)) {

                setIsDropOpen(false);
            }
        };
        document.addEventListener("mousedown", handleClickOutside);
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, []);

    return (
        <li className={"nav-item dropdown"} ref={dropRef}>
            <Link className="nav-link dropdown-toggle"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  onClick={toggleDrop}
            >
                {label}
            </Link>
            <ul className={classNames(
                "dropdown-menu",
                "dropdown-menu-end",
                // "text-small",
                {"show": isDropOpen}
            )}>
                {children}
            </ul>
        </li>
    );
}

export default NavbarDropdown;
