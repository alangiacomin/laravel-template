import {FC, ReactNode} from "react";
import Page from "../components/Page/Page.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";

const ExamplePage: FC = (): ReactNode => {
    const __ = useTranslations();

    return (
        <Page browserTitle={__('example_page.browser_title')}>
            <section className="py-5">
                <div className="container">
                    <h1 className="display-5 fw-bold mb-4">{__('example_page.title')}</h1>
                    <p className="lead text-muted mb-3">
                        {__('example_page.paragraph_1')}
                    </p>
                    <p className="text-muted mb-3">
                        {__('example_page.paragraph_2')}
                    </p>
                    <p className="text-muted mb-0">
                        {__('example_page.paragraph_3')}
                    </p>
                </div>
            </section>
        </Page>
    );
}

export default ExamplePage;
