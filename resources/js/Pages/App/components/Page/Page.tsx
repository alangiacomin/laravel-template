import {FC, ReactNode} from "react";
import classNames from "classnames";
import BuiltInBrowserComponent from "../../../../components/BuiltInBrowserComponent/BuiltInBrowserComponent.tsx";

type PageProps = {
    title?: ReactNode;
    browserTitle?: string;
    className?: string;
    children?: ReactNode;
}

const Page: FC<PageProps> = ({
                                 title,
                                 browserTitle,
                                 className,
                                 children,
                             }: PageProps): ReactNode => {


    return (
        <>
            <BuiltInBrowserComponent title={browserTitle ?? ''}/>
            <div className={classNames("", className)}>
                {title && (<h1 className="mb-3">{title}</h1>)}
                {children}
            </div>
        </>);
}


export default Page;
