import {FC, ReactNode} from "react";
import {RoleEnum} from "../../../types/generated";
import Input from "../../../components/Input/Input.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";

type UserRolesProps = {
    readOnly: boolean;
}
const UserRoles: FC<UserRolesProps> = ({readOnly}: UserRolesProps): ReactNode => {

    const __ = useTranslations();
    return (
        <div className="card mb-4">
            <div className="card-header fw-semibold">
                {__('admin.roles')}
            </div>
            <div className="card-body">
                <div className="row">
                    {Object.values(RoleEnum).map((role) => (
                        <div className="col-md-4" key={`role_${role}`}>
                            <Input
                                type="checkbox"
                                name={`roles.${role}`}
                                label={role}
                                labelOnLeft
                                disabled={readOnly}
                            />
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}

export default UserRoles;
