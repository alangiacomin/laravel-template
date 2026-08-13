import {FC, ReactNode, useState} from "react";
import Header from "./Header.tsx";
import Sidebar from "./Sidebar.tsx";

type LayoutProps = {
    children: ReactNode;
}

const Layout: FC<LayoutProps> = ({children}: LayoutProps): ReactNode => {
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    const toggleSidebar = () => {
        setIsSidebarOpen(!isSidebarOpen);
    };

    const closeSidebar = () => {
        setIsSidebarOpen(false);
    };

    return (
        <div className="vh-100">
            <div className="d-flex flex-column h-100">
                <Header toggleSidebar={toggleSidebar} isSidebarOpen={isSidebarOpen}/>
                <div className="d-flex flex-grow-1 overflow-hidden position-relative">
                    <Sidebar isOpen={isSidebarOpen} closeSidebar={closeSidebar}/>
                    <main className="flex-grow-1 bg-light p-0 overflow-auto">
                        {children}
                    </main>
                    {/* Mobile overlay when the sidebar is open */}
                    {isSidebarOpen && (
                        <div
                            className="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50 d-lg-none"
                            style={{zIndex: 1040}}
                            onClick={toggleSidebar}
                        />
                    )}
                </div>
            </div>
        </div>
    );
}

export default Layout;
