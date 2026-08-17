import {FC, ReactNode} from 'react';
import Page from "../components/Page/Page.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";

const VerificationNotice: FC = (): ReactNode => {
    const __ = useTranslations();
    
    return (
        <Page browserTitle={__('verification.notice_title')}>
            <div className="container">
                <div className="row justify-content-center mt-5">
                    <div className="col-md-6 text-center">
                        <h2>{__('verification.notice_heading')}</h2>
                        <p className="mt-3">
                            {__('verification.notice_message')}
                        </p>
                        <p>
                            {__('verification.notice_instruction')}
                        </p>
                    </div>
                </div>
            </div>
        </Page>
    );
};

export default VerificationNotice;
