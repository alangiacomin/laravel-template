import type { FC, ReactNode } from "react";
import Input from "../../../components/Input/Input.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";

type UserDatiProps = {
  readOnly: boolean;
};
const UserDati: FC<UserDatiProps> = ({
  readOnly,
}: UserDatiProps): ReactNode => {
  const __ = useTranslations();
  return (
    <div className="card mb-4">
      <div className="card-header fw-semibold">{__("admin.user_data")}</div>
      <div className="card-body">
        <div className="row">
          <div className="col-md-6">
            <Input
              className="form-control"
              name={"name"}
              label={__("user.name")}
              disabled={readOnly}
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default UserDati;
