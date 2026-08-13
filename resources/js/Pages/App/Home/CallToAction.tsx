import {FC} from "react";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {Link} from "@inertiajs/react";
import {useRoutes} from "../../../hooks/useRoutes.ts";

const CallToAction: FC = () => {

    const __ = useTranslations();
    const routes = useRoutes();

    return (
        <section className="bg-white py-5 guest-only" id="cta-section">
            <div className="container">
                <div className="row align-items-center">
                    <div className="col-lg-7">
                        <h2 className="fw-bold mb-4 text-primary">{__('home.cta_title')}</h2>
                        <p className="lead mb-4 text-muted">{__('home.cta_subtitle')}</p>
                        <div className="row g-4">
                            <div className="col-md-6">
                                <div className="p-3 border-start border-primary border-4 bg-light rounded-end">
                                    <h5 className="fw-bold"><i className="bi bi-cart-x me-2 text-primary"></i>
                                        {__('home.cta_stop_dupes_title')}
                                    </h5>
                                    <p className="small text-muted mb-0">{__('home.cta_stop_dupes_subtitle')}</p>
                                </div>
                            </div>
                            <div className="col-md-6">
                                <div className="p-3 border-start border-primary border-4 bg-light rounded-end">
                                    <h5 className="fw-bold"><i className="bi bi-graph-up me-2 text-primary"></i>
                                        {__('home.cta_stats_title')}
                                    </h5>
                                    <p className="small text-muted mb-0">{__('home.cta_stats_subtitle')}</p>
                                </div>
                            </div>
                            <div className="col-md-6">
                                <div className="p-3 border-start border-primary border-4 bg-light rounded-end">
                                    <h5 className="fw-bold"><i className="bi bi-layers me-2 text-primary"></i>
                                        {__('home.cta_editions_title')}
                                    </h5>
                                    <p className="small text-muted mb-0">{__('home.cta_editions_subtitle')}</p>
                                </div>
                            </div>
                            <div className="col-md-6">
                                <div className="p-3 border-start border-primary border-4 bg-light rounded-end">
                                    <h5 className="fw-bold"><i className="bi bi-cloud-check me-2 text-primary"></i>
                                        {__('home.cta_cloud_title')}
                                    </h5>
                                    <p className="small text-muted mb-0">{__('home.cta_cloud_subtitle')}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-lg-5 mt-5 mt-lg-0 text-center">
                        <div className="card border-primary border-2 p-5 shadow-lg">
                            <i className="bi bi-person-plus display-1 text-primary mb-4"></i>
                            <h3 className="fw-bold">{__('home.cta_start_today_title')}</h3>
                            <p className="text-muted mb-4">{__('home.cta_start_today_subtitle')}</p>
                            <div className="d-grid gap-2">
                                <Link className="btn btn-primary btn-lg shadow"
                                      href={routes.app.register()}
                                >
                                    {__('global.register')}
                                </Link>
                                <p className="small text-muted mt-2">
                                    {__('home.cta_already_have_account')} <Link
                                    href={routes.app.login()}>{__('global.login')}</Link>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default CallToAction;
