import {FC, ReactNode} from "react";
import {AdminUserData} from "../../../types/generated";
import useTranslations from "../../../hooks/useTranslations.tsx";

type UserTitleProps = {
    user: AdminUserData;
}
const UserTitle: FC<UserTitleProps> = ({user}: UserTitleProps): ReactNode => {

    const __ = useTranslations();
    return (
        <div className="card mb-4">
            <div className="card-body d-flex align-items-center gap-4">
                <img src={user.avatar}
                     className="rounded-circle border"
                     alt={__('user.avatar_user')}
                     width="96"
                     height="96"/>

                <div className="flex-grow-1">
                    <div className="d-flex align-items-center gap-2 mb-1">
                        <h5 className="mb-0">{user.name}</h5>
                        {user.isVerified
                            ? <span className="badge bg-success">{__('user.status_verified')}</span>
                            : <span className="badge bg-warning text-dark">{__('user.status_unverified')}</span>
                        }
                        {user.isBanned
                            ? <span className="badge bg-danger ms-2">{__('user.status_banned')}</span>
                            : null}
                    </div>

                    <div className="text-muted">{user.email}</div>
                    <div className="small text-muted">{__('user.id')}: {user.id}</div>
                </div>
            </div>
        </div>
    );
}

export default UserTitle;
