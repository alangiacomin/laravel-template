import {FC, ReactNode} from "react";
import {Link} from "@inertiajs/react";
import classNames from "classnames";

export type BreadcrumbElement = {
    name: string,
    href: string,
}

type BreadcrumbProps = {
    elements: BreadcrumbElement[],
}

const Breadcrumb: FC<BreadcrumbProps> = ({elements}: BreadcrumbProps): ReactNode => {
    return (
        <nav aria-label="breadcrumb">
            <ol className="breadcrumb">
                {elements.map((element, index: number) => {
                    const isLast = index === elements.length - 1;
                    return (
                        <li className={classNames("breadcrumb-item", {"active": isLast})}
                            key={`breadcrumb_${element.name}`}>
                            {isLast ? element.name : <Link href={element.href}>{element.name}</Link>}
                        </li>
                    );
                })}
            </ol>
        </nav>
    )
}

export default Breadcrumb;
