import {FC, ReactNode} from "react";
import Navbar from "../Navbar/Navbar.tsx";

type LayoutProps = {
    children: ReactNode;
}
const Layout: FC<LayoutProps> = ({children}: LayoutProps): ReactNode => {

    return (
        <div className="d-flex flex-column min-vh-100">
            <Navbar/>
            <main className="">
                {children}
            </main>

            <footer className="bg-dark text-white py-4 mt-auto">
                <div className="container text-center">
                    <p className="mb-0">&copy; 2026 Company Inc.</p>
                </div>
            </footer>
        </div>
    );
}

export default Layout;
