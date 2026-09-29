import {FC, ReactNode} from "react";

type MetricCardProps = {
    title: string;
    subtitle: string;
    count: number;
}

const MetricCard: FC<MetricCardProps> = ({title, subtitle, count}): ReactNode => (
    <div className="card shadow-sm h-100">
        <div className="card-body">
            <h5 className="mb-1">{title}</h5>
            <p className="text-muted mb-2">{subtitle}</p>
            <h3 className="card-title mb-0">{count}</h3>
        </div>
    </div>
);

export default MetricCard;
