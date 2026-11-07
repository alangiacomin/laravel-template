import type { FC, ReactNode } from "react";

type MetricCardProps = {
  title: string;
  subtitle: string;
  count: number;
};

const MetricCard: FC<MetricCardProps> = ({
  title,
  subtitle,
  count,
}): ReactNode => (
  <div className="card shadow-sm h-100">
    <div className="card-body">
      <h3 className="h5 mb-1">{title}</h3>
      <p className="text-muted mb-2">{subtitle}</p>
      <p className="h3 card-title mb-0">{count}</p>
    </div>
  </div>
);

export default MetricCard;
