import { Link } from "@inertiajs/react";
import classNames from "classnames";
import {
  type FC,
  type MouseEvent as ReactMouseEvent,
  type ReactNode,
  useEffect,
  useRef,
  useState,
} from "react";

type NavbarDropdownProps = {
  label: ReactNode;
  className?: string;
  children: ReactNode;
};

const NavbarDropdown: FC<NavbarDropdownProps> = ({
  label,
  children,
}: NavbarDropdownProps): ReactNode => {
  const [isDropOpen, setIsDropOpen] = useState(false);
  const dropRef = useRef<HTMLLIElement | null>(null);

  const toggleDrop = (e: ReactMouseEvent) => {
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
      <Link
        className="nav-link dropdown-toggle"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        onClick={toggleDrop}
      >
        {label}
      </Link>
      <ul
        className={classNames(
          "dropdown-menu",
          "dropdown-menu-end",
          // "text-small",
          { show: isDropOpen },
        )}
      >
        {children}
      </ul>
    </li>
  );
};

export default NavbarDropdown;
