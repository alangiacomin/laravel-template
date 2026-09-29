import type {
    CatalogComicPublicationData,
    CatalogOptionData,
} from "../../../types/generated";

type Props = {
    testata: CatalogOptionData;
    titolo: string;
    pubblicazioni: CatalogComicPublicationData[];
};

const AlboCard = ({
                      testata,
                      titolo,
                      pubblicazioni,
                  }: Props) => {
    const locale =
        window.location.pathname.split("/")[1] === "en"
            ? "en"
            : "it";

    const dateFormatter = new Intl.DateTimeFormat(locale, {
        dateStyle: "long",
    });

    return (
        <div className="card h-100">
            <div className="card-body">
                <div className="text-body-secondary">
                    {testata.titolo}
                </div>

                <h5 className="card-title">
                    {titolo}
                </h5>

                <div className="card-text mt-2">
                    {pubblicazioni.map((pub, index) => (
                        <div
                            className="row"
                            key={`${pub.serie.id}-${pub.numero}-${index}`}
                        >
                            <div className="col-sm-3">
                                {pub.serie.titolo}
                            </div>

                            <div className="col-sm-5">
                                {pub.numeroGruppo !== null
                                    ? `${pub.numeroGruppo}-${pub.numero}`
                                    : pub.numero}
                            </div>

                            <div className="col-sm-4 text-body-secondary small">
                                {pub.dataPubblicazione
                                    ? dateFormatter.format(
                                        new Date(
                                            pub.dataPubblicazione
                                        )
                                    )
                                    : "—"}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default AlboCard;
