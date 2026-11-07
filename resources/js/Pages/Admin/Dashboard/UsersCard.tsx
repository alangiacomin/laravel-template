import type { FC, ReactNode } from "react";
import useTranslations from "../../../hooks/useTranslations.tsx";

type UsersCardProps = {
  title: string;
  subtitle: string;
  count: number;
  unverifiedCount: number;
};

const UsersCard: FC<UsersCardProps> = ({
  title,
  subtitle,
  count,
  unverifiedCount,
}: UsersCardProps): ReactNode => {
  const __ = useTranslations();

  return (
    <div className="card shadow-sm">
      <div className="card-body">
        <h3 className="h5 mb-1">{title}</h3>
        <p className="text-muted mb-2">{subtitle}</p>
        <p className="h3 card-title">{count ?? 0}</p>
        {unverifiedCount ? (
          <span className="badge bg-warning text-dark mt-2">
            {unverifiedCount} {__("admin.unverified_count")}
          </span>
        ) : (
          <span className="badge bg-success mt-2">
            {__("admin.all_verified")}
          </span>
        )}
      </div>
    </div>
  );
};

export default UsersCard;
