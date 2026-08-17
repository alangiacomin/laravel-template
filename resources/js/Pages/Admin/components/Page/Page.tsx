import {FC, ReactNode} from "react";
import BuiltInBrowserComponent from "../../../../components/BuiltInBrowserComponent/BuiltInBrowserComponent.tsx";
import Breadcrumb, {BreadcrumbElement} from "../Breadcrumb/Breadcrumb.tsx";

type PageProps = {
    title?: ReactNode;
    subtitle?: ReactNode;
    browserTitle?: string;
    breadcrumb?: BreadcrumbElement[];
    children?: ReactNode;
}
const Page: FC<PageProps> = ({
                                 title,
                                 subtitle,
                                 browserTitle,
                                 breadcrumb,
                                 children,
                             }: PageProps): ReactNode => {


    return (
        <>
            <BuiltInBrowserComponent title={browserTitle ?? ''}/>
            <div className="container py-4">
                {breadcrumb && (
                    <Breadcrumb elements={breadcrumb}/>
                )}
                {title && (
                    <div className="mb-4">
                        <h2 className="mb-1">{title}</h2>
                        {subtitle && (
                            <p className="text-muted">{subtitle}</p>
                        )}
                    </div>
                )}
                {children}
            </div>
        </>);
}


export default Page;
