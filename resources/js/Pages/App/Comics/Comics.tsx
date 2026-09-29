import {FormEvent, useState} from "react";
import {router, usePage} from "@inertiajs/react";
import Page from "../components/Page/Page.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import type {
    CatalogComicData,
    CatalogOptionData,
    CatalogSerieOptionData
} from "../../../types/generated";
import "./Comics.css";
import AlboCard from "./AlboCard.tsx";

type Filters = {
    q: string;
    testata_id: number | null;
    serie_id: number | null;
    year: number | null;
};

type Pagination = {
    current_page: number;
    last_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    data: CatalogComicData[];
};

type Props = {
    comics: Pagination;
    testate: CatalogOptionData[];
    serie: CatalogSerieOptionData[];
    years: number[];
    filters: Filters;
};

const Comics = () => {
    const __ = useTranslations();
    const routes = useRoutes();
    const {comics, testate, serie, years, filters} = usePage<Props>().props;

    const [query, setQuery] = useState(filters.q);
    const [testataId, setTestataId] = useState(
        filters.testata_id?.toString() ?? ""
    );
    const [serieId, setSerieId] = useState(
        filters.serie_id?.toString() ?? ""
    );
    const [year, setYear] = useState(
        filters.year?.toString() ?? ""
    );

    const submitSearch = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        router.get(
            routes.app.comics(),
            {
                q: query.trim(),
                testata_id: testataId,
                serie_id: serieId,
                year,
            },
            {
                preserveScroll: true,
                replace: true,
            }
        );
    };

    return (
        <Page
            browserTitle={__("comics.title")}
            className="comic-catalog"
        >
            <div className="container pb-5">
                <header className="comic-catalog__hero rounded-4 p-4 p-md-5 mb-4">
                    <h1 className="comic-catalog__eyebrow h2">
                        {__("comics.title")}
                    </h1>

                    <p className="lead mb-4">
                        {__("comics.subtitle")}
                    </p>

                    <form onSubmit={submitSearch} role="search">
                        <label
                            className="form-label fw-semibold"
                            htmlFor="comic-search"
                        >
                            {__("comics.search")}
                        </label>

                        <div className="input-group input-group-lg">
                            <input
                                id="comic-search"
                                className="form-control"
                                type="search"
                                maxLength={200}
                                value={query}
                                placeholder={__(
                                    "comics.search_placeholder"
                                )}
                                onChange={event =>
                                    setQuery(event.target.value)
                                }
                            />

                            <button
                                className="btn btn-primary px-4"
                                type="submit"
                            >
                                {__("comics.apply_filters")}
                            </button>
                        </div>

                        <div className="row g-3 mt-3">
                            <div className="col-12 col-sm-6 col-lg-4">
                                <label
                                    className="form-label"
                                    htmlFor="comic-testata"
                                >
                                    {__("comics.testata")}
                                </label>

                                <select
                                    id="comic-testata"
                                    className="form-select"
                                    value={testataId}
                                    onChange={event => {
                                        setTestataId(event.target.value);
                                        setTimeout(
                                            () =>
                                                event.target.form?.requestSubmit(),
                                            250
                                        );
                                    }}
                                >
                                    <option value="">
                                        {__("comics.all_testate")}
                                    </option>

                                    {testate.map(item => (
                                        <option
                                            key={item.id}
                                            value={item.id}
                                        >
                                            {item.titolo}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="col-12 col-sm-6 col-lg-4">
                                <label
                                    className="form-label"
                                    htmlFor="comic-serie"
                                >
                                    {__("comics.serie")}
                                </label>

                                <select
                                    id="comic-serie"
                                    className="form-select"
                                    value={serieId}
                                    onChange={event => {
                                        setSerieId(event.target.value);
                                        setTimeout(
                                            () =>
                                                event.target.form?.requestSubmit(),
                                            250
                                        );
                                    }}
                                >
                                    <option value="">
                                        {__("comics.all_series")}
                                    </option>

                                    {serie.map(item => (
                                        <option
                                            key={item.id}
                                            value={item.id}
                                        >
                                            {item.titolo}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="col-12 col-sm-6 col-lg-4">
                                <label
                                    className="form-label"
                                    htmlFor="comic-year"
                                >
                                    {__("comics.year")}
                                </label>

                                <select
                                    id="comic-year"
                                    className="form-select"
                                    value={year}
                                    onChange={event => {
                                        setYear(event.target.value);
                                        setTimeout(
                                            () =>
                                                event.target.form?.requestSubmit(),
                                            250
                                        );
                                    }}
                                >
                                    <option value="">
                                        {__("comics.all_years")}
                                    </option>

                                    {years.map(item => (
                                        <option key={item} value={item}>
                                            {item}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {(query || testataId || serieId || year) && (
                            <a
                                className="d-inline-block mt-3 text-white"
                                href={routes.app.comics()}
                            >
                                {__("comics.clear_filters")}
                            </a>
                        )}
                    </form>
                </header>

                <div className="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 className="h5 mb-0">
                        {__("comics.results", {
                            count: comics.total,
                        })}
                    </h2>

                    {comics.last_page > 1 && (
                        <span className="text-body-secondary">
                            {__("comics.page_of", {
                                current: comics.current_page,
                                last: comics.last_page,
                            })}
                        </span>
                    )}
                </div>

                {comics.data.length > 0 ? (
                    <div className="row row-cols-1 row-cols-md-3 g-4">
                        {comics.data.map(comic => (
                            <div
                                key={comic.id}
                                className="col"
                            >
                                <AlboCard
                                    testata={comic.testata}
                                    titolo={comic.titolo}
                                    pubblicazioni={comic.pubblicazioni}
                                />
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="comic-catalog__empty rounded-3 text-center p-5">
                        <div
                            className="comic-card__mark mx-auto mb-3"
                            aria-hidden="true"
                        >
                            ?
                        </div>

                        <h2 className="h4">
                            {__("comics.no_results_title")}
                        </h2>

                        <p className="mb-0 text-body-secondary">
                            {__("comics.no_results")}
                        </p>
                    </div>
                )}

                {(comics.prev_page_url || comics.next_page_url) && (
                    <nav
                        className="d-flex justify-content-between align-items-center mt-4"
                        aria-label={__("comics.title")}
                    >
                        {comics.prev_page_url ? (
                            <a
                                className="btn btn-outline-primary"
                                href={comics.prev_page_url}
                            >
                                {__("comics.previous")}
                            </a>
                        ) : (
                            <span />
                        )}

                        {comics.next_page_url ? (
                            <a
                                className="btn btn-outline-primary"
                                href={comics.next_page_url}
                            >
                                {__("comics.next")}
                            </a>
                        ) : (
                            <span />
                        )}
                    </nav>
                )}
            </div>
        </Page>
    );
};

export default Comics;
