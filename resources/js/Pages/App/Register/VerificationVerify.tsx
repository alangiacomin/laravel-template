import {FC, ReactNode, useEffect} from 'react';
import Page from "../components/Page/Page.tsx";
import {Link, router} from "@inertiajs/react";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";

const VerificationVerify: FC = (): ReactNode => {

    const routes = useRoutes();
    const __ = useTranslations();

    const redirectDelay = 5;

    useEffect(() => {
        const timerId = setTimeout(() => {
            router.visit(routes.app.home());
        }, redirectDelay * 1000);
        return () => {
            clearTimeout(timerId);
        }
    }, []);

    return (
        <Page browserTitle={__('verification.verified_title')}>
            <div className="container">
                <div className="row justify-content-center mt-5">
                    <div className="col-md-6 text-center">
                        <h2 className="mb-3">{__('verification.verified_heading')}</h2>

                        <p className="mb-3">
                            {__('verification.verified_message')}
                        </p>

                        <p>
                            {__('verification.redirect_message', {seconds: redirectDelay})}<br/>
                            {__('verification.click_here')} <Link href={routes.app.home()}>{__('verification.here')}</Link>.
                        </p>
                    </div>
                </div>
            </div>
        </Page>
    );
};

export default VerificationVerify;
