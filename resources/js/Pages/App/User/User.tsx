import {FC, ReactNode, useState} from "react";
import Page from "../components/Page/Page.tsx";
import EditProfileModal from "./EditProfileModal.tsx";
import Button from "../../../components/Button/Button.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {UserData} from "../../../types/generated";

type UserProps = {
    user: UserData,
}

const User: FC<UserProps> = ({user}: UserProps): ReactNode => {

    const [showEditModal, setShowEditModal] = useState(false);
    const __ = useTranslations();

    return (
        <>
            <Page className="py-5" browserTitle={__('user.browser_title')}>
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-8 col-lg-9 col-md-10">

                            <div className="card shadow-sm">
                                <div className="card-body">

                                    {/* Header */}
                                    <div className="d-flex align-items-center mb-4">
                                        <img
                                            src={user.avatar}
                                            alt={`${__('user.avatar_alt')} ${user.name}`}
                                            width={80}
                                            height={80}
                                            className="rounded-circle border me-3"
                                        />
                                        <div>
                                            <h5 className="mb-1">{user.name}</h5>
                                            <div className="text-muted small">
                                                {user.email}
                                            </div>
                                        </div>
                                    </div>

                                    <hr className="my-4"/>

                                    {/* Contenuto responsivo */}
                                    <div className="row">
                                        <div className="col-12 col-md-6 mb-3 mb-md-0">
                                            <dl className="row mb-0">
                                                <dt className="col-5 text-muted">{__("user.name")}</dt>
                                                <dd className="col-7">{user.name}</dd>

                                                <dt className="col-5 text-muted">{__("user.role")}</dt>
                                                <dd className="col-7">
                                                    {/*{user.role ?? "—"}*/}
                                                    {"—"}
                                                </dd>
                                            </dl>
                                        </div>

                                        <div className="col-12 col-md-6">
                                            <dl className="row mb-0">
                                                <dt className="col-5 text-muted">{__("user.registered_at")}</dt>
                                                <dd className="col-7">
                                                    {user.created_at}
                                                </dd>

                                                <dt className="col-5 text-muted">{__("user.status")}</dt>
                                                <dd className="col-7">
                                                    <span
                                                        className={`badge ${
                                                            user.isVerified
                                                                ? "bg-success"
                                                                : "bg-warning text-dark"
                                                        }`}
                                                    >
                                                        {user.isVerified
                                                            ? __('user.status_verified')
                                                            : __('user.status_unverified')}
                                                    </span>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>

                                    <hr className="my-4"/>

                                    {/* Azioni */}
                                    <div className="d-flex justify-content-end gap-2">
                                        <Button
                                            className="btn-outline-primary"
                                            onClick={() => {
                                                setShowEditModal(true)
                                            }}
                                        >
                                            {__("user.edit_profile")}
                                        </Button>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </Page>
            {showEditModal && (
                <EditProfileModal
                    show={showEditModal}
                    user={user}
                    onClose={() => {
                        setShowEditModal(false)
                    }}
                />)}
        </>
    );
}

export default User;
