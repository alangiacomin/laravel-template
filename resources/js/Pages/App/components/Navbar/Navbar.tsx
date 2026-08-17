import {FC, ReactNode, useState} from "react";
import classNames from "classnames";
import NavbarItem from "./NavbarItem.tsx";
import useAuth from "../../../../hooks/useAuth.tsx";
import {Link, usePage} from "@inertiajs/react";
import {GateEnum} from "../../../../types/generated";
import NavbarDropdown from "./NavbarDropdown.tsx";
import NavbarDropdownItem from "./NavbarDropdownItem.tsx";
import NavbarDropdownDivider from "./NavbarDropdownDivider.tsx";
import {useRoutes} from "../../../../hooks/useRoutes.ts";
import useTranslations from "../../../../hooks/useTranslations.tsx";
import useAppData from "../../../../hooks/useAppData.tsx";
import {SharedPageProps} from "../../../page.types.ts";

const Navbar: FC = (): ReactNode => {
    const [isBurgerOpen, setIsBurgerOpen] = useState(false);
    const user = useAuth();
    const routes = useRoutes();
    const __ = useTranslations();
    const {appName} = useAppData();
    const {props} = usePage<SharedPageProps>();
    const currentLocale = props.locale || props.defaultLocale;

    const toggleBurger = () => {
        setIsBurgerOpen(!isBurgerOpen);
    };
    const closeBurger = () => {
        setIsBurgerOpen(false);
    }

    const navConfig: { [key: string]: { path: string, label: string, permission?: GateEnum } } = {
        home: {
            path: routes.app.home(),
            label: __('home'),
        },
        examplePage: {
            path: routes.app.examplePage(),
            label: 'ExamplePage',
        },
    };

    const locales = [
        {value: 'it', label: 'Italiano'},
        {value: 'en', label: 'English'},
        {value: 'fr', label: 'Français'},
    ]

    const changeLocale = (e: MouseEvent, locale: string) => {
        e.preventDefault();
        routes.changeLocale(locale == 'it' ? null : locale);
    }

    return (
        <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
            <div className="container">
                <Link className="navbar-brand fw-bold text-uppercase" href={navConfig.home.path}>{appName}</Link>
                <button className="navbar-toggler" type="button" onClick={toggleBurger}>
                    <span className="navbar-toggler-icon"></span>
                </button>
                <div className={classNames("navbar-collapse", {"collapse": !isBurgerOpen})} id="navbarSupportedContent">
                    <ul className="navbar-nav me-auto mb-2 mb-lg-0">
                        {Object.entries(navConfig).map(([key, n]) => {
                            if (!n.permission || (user && user.can(n.permission))) {
                                return (
                                    <NavbarItem key={key} to={n.path} onClick={closeBurger}>
                                        {n.label}
                                    </NavbarItem>);
                            }
                            return null;
                        })}
                    </ul>
                    <ul className="navbar-nav ms-auto mb-2 mb-lg-0">
                        {!user && (
                            <>
                                <li className="nav-item">
                                    <Link className="nav-link px-3"
                                          href={routes.app.register()}
                                          onClick={closeBurger}
                                    >
                                        {__('register')}
                                    </Link>
                                </li>
                                <li className="nav-item">
                                    <Link className="btn btn-primary"
                                          href={routes.app.login()}
                                          onClick={closeBurger}
                                    >
                                        {__('login')}
                                    </Link>
                                </li>
                            </>
                        )}
                        {user && (
                            <NavbarDropdown label={(
                                <>
                                    {/*<img src={user.avatar} alt={user.name} width="32" height="32"*/}
                                    {/*     className="rounded-circle"/>*/}
                                    <span className="ms-2 text-white">{user.name}</span>
                                </>
                            )}>
                                <NavbarDropdownItem to={routes.app.user()}
                                                    onClick={closeBurger}>{__('user')}</NavbarDropdownItem>
                                <NavbarDropdownItem
                                    to={routes.admin.dashboard()}
                                    onClick={closeBurger}>{__('admin.admin_panel')}</NavbarDropdownItem>
                                <NavbarDropdownDivider/>
                                <NavbarDropdownItem to={routes.app.logout()}
                                                    onClick={closeBurger}>{__('logout')}</NavbarDropdownItem>
                            </NavbarDropdown>
                        )}
                        <NavbarDropdown className={'me-3'} label={(
                            <span className={'text-white'}>
                            <i className="bi bi-globe2"></i> {currentLocale.toUpperCase()}
                        </span>
                        )}>
                            {locales.map((l, index) => (
                                <NavbarDropdownItem key={index} to={'#'} onClick={(e) => {
                                    changeLocale(e, l.value)
                                }}>
                                    {l.label} ({l.value.toUpperCase()})
                                </NavbarDropdownItem>
                            ))}
                        </NavbarDropdown>
                    </ul>
                </div>
            </div>
        </nav>
    );
}

export default Navbar;
