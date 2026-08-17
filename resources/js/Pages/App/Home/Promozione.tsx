import useTranslations from "../../../hooks/useTranslations.tsx";
import Feature from "./Feature.tsx";

const Promozione = () => {

    const __ = useTranslations();

    const features = [
        {
            icon: 'bi-search',
            title: __('home.feat_struttura_title'),
            description: __('home.feat_struttura_subtitle')
        },
        {
            icon: 'bi-stack',
            title: __('home.feat_auth_title'),
            description: __('home.feat_auth_subtitle')
        },
        {
            icon: 'bi-graph-up-arrow',
            title: __('home.feat_multilingua_title'),
            description: __('home.feat_multilingua_subtitle')
        },
    ];

    return (
        <section className="py-5" id="features">
            <div className="container">
                <div className="text-center mb-5">
                    <h2 className="fw-bold">{__('home.promo_title')}</h2>
                    <p className="text-muted">{__('home.promo_subtitle')}</p>
                </div>
                <div className="row g-4 text-center">
                    {features.map((feature, index) => (
                        <Feature key={index} icon={feature.icon} title={feature.title}
                                 description={feature.description}/>
                    ))}
                </div>
            </div>
        </section>);
}

export default Promozione;
