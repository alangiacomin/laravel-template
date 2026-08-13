import {FC, ReactNode} from "react";
import Page from "../components/Page/Page.tsx";
import useErrors from "../../../hooks/useErrors.tsx";

const ErrorPage: FC<{ status: number }> = ({status}: { status: number }): ReactNode => {
    const {title, description, hint} = useErrors(status);

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
