import {FC, ReactNode} from "react";
import {Link} from "@inertiajs/react";
import Page from "../components/Page/Page.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import useErrors from "../../../hooks/useErrors.tsx";

const ErrorPage: FC<{ status: number, reason?: string }> = ({status, reason}: {
    status: number,
    reason?: string
}): ReactNode => {

    const __ = useTranslations();
    const routes = useRoutes();
    const {title, description, hint} = useErrors(status, reason);

    return (
        <Page>
            <div className="container">
                <div className="row justify-content-center mt-5">
                    <div className="col-md-6 text-center">
                        <div className="display-4 fw-bold text-secondary mb-3">
                            {status}
                        </div>

                        <h2 className="mb-3">{title}</h2>

                        <p className="text-muted mb-4">{description}</p>

                        {reason !== __('error.account_banned') && (
                            <Link href={routes.app.home()} className="btn btn-primary">
                                {__('error.back_to_site')}
                            </Link>
                        )}

                        {hint && (
                            <p className="text-muted mt-3 small">
                                {hint}
                            </p>
                        )}
                    </div>
                </div>
            </div>
        </Page>
    );
}

export default ErrorPage;
