import classnames from "classnames";
import {FC} from "react";

type FeatureProps = {
    icon: string;
    title: string;
    description: string;
}

const Feature: FC<FeatureProps> = ({icon, title, description}: FeatureProps) => {

    return (
        <div className="col-md-4">
            <div className={classnames(
                "card h-100 p-4",
                "border-top border-4 border-primary"
            )}>
                <div className="feature-icon"><i className={classnames("bi", icon)}></i></div>
                <h3>{title}</h3>
                <p className="text-muted">{description}</p>
            </div>
        </div>
    );
}

export default Feature;
