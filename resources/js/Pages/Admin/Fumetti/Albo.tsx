import {FC, ReactNode, useState} from "react";
import {Link, router, useForm, usePage} from "@inertiajs/react";
import Page from "../components/Page/Page.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Input from "../../../components/Input/Input.tsx";
import Button from "../../../components/Button/Button.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {localizedRoute} from "../../../localizedRoute.ts";
import type {AlboDetailData, AlboInputData, AlboSerieInputData, SerieWithTestataData} from "../../../types/generated";

type Props = { albo: AlboDetailData; serie: SerieWithTestataData[] };

const Albo: FC = (): ReactNode => {
    const __ = useTranslations();
    const routes = useRoutes();
    const {albo, serie} = usePage<Props>().props;
    const initial: AlboSerieInputData[] = albo.serie.map(item => ({
        serie_id: item.id,
        numero: item.associazione.numero,
        numero_gruppo: item.associazione.numero_gruppo,
        data_pubblicazione: item.associazione.data_pubblicazione,
    }));
    const [associations, setAssociations] = useState(initial);
    const form = useForm<AlboInputData>({
        titolo: albo.titolo,
        serie: initial,
    });

    const update = (next: AlboSerieInputData[]) => {
        setAssociations(next);
        form.setData("serie", next);
    };

    const addSerie = () => {
        const nextSerie = serie.find(item => !associations.some(association => association.serie_id === item.id));

        if (!nextSerie) {
            return;
        }

        update([...associations, {
            serie_id: nextSerie.id,
            numero: 1,
            numero_gruppo: null,
            data_pubblicazione: null,
        }]);
    };

    return <Page
        title={albo.titolo}
        browserTitle={albo.titolo}
        breadcrumb={[
            {name: __("admin.admin_panel"), href: routes.admin.dashboard()},
            {name: __("admin.albi"), href: routes.admin.albi()},
            {name: albo.titolo, href: ""},
        ]}
    >
        <Form form={form}>
            <Input name="titolo" label={__("admin.title")}/>
            <h4>{__("admin.series")}</h4>
            {associations.map((item, index) => (
                <div className="card mb-3" key={`${item.serie_id}-${index}`}>
                    <div className="card-body row">
                        <div className="col-md-3">
                            <label className="form-label">{__("admin.series")}</label>
                            <select
                                className="form-select"
                                value={item.serie_id}
                                onChange={event => update(associations.map((association, itemIndex) => itemIndex === index
                                    ? {...association, serie_id: Number(event.target.value)}
                                    : association))}
                            >
                                {serie.map(item => (
                                    <option key={item.id} value={item.id}>
                                        {item.testata.titolo} - {item.titolo}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="col-md-2">
                            <Input
                                name={`serie.${index}.numero`}
                                type="number"
                                label={__("admin.number")}
                                value={item.numero}
                                onChange={event => update(associations.map((association, itemIndex) => itemIndex === index
                                    ? {...association, numero: Number(event.target.value)}
                                    : association))}
                            />
                        </div>
                        <div className="col-md-2">
                            <Input
                                name={`serie.${index}.numero_gruppo`}
                                type="number"
                                label={__("admin.number_group")}
                                value={item.numero_gruppo ?? ""}
                                onChange={event => update(associations.map((association, itemIndex) => itemIndex === index
                                    ? {...association, numero_gruppo: event.target.value ? Number(event.target.value) : null}
                                    : association))}
                            />
                        </div>
                        <div className="col-md-3">
                            <Input
                                type="date"
                                name={`serie.${index}.data_pubblicazione`}
                                label={__("admin.publication_date")}
                                value={item.data_pubblicazione ?? ""}
                                onChange={event => update(associations.map((association, itemIndex) => itemIndex === index
                                    ? {...association, data_pubblicazione: event.target.value || null}
                                    : association))}
                            />
                        </div>
                        <div className="col-md-2 d-flex align-items-center">
                            <button
                                type="button"
                                className="btn btn-outline-danger"
                                onClick={() => update(associations.filter((_, itemIndex) => itemIndex !== index))}
                            >
                                ×
                            </button>
                        </div>
                    </div>
                </div>
            ))}
            <button
                type="button"
                className="btn btn-outline-secondary mb-4"
                disabled={serie.every(item => associations.some(association => association.serie_id === item.id))}
                onClick={addSerie}
            >
                {__("admin.add_series")}
            </button>
            <div className="d-flex justify-content-between gap-2">
                <button
                    type="button"
                    className="btn btn-outline-danger"
                    onClick={() => router.delete(localizedRoute("admin.albo.delete", {id: albo.id}), {
                        onBefore: () => window.confirm(__("admin.delete_confirm")),
                    })}
                >
                    {__("delete")}
                </button>
                <div className="d-flex gap-2">
                    <Link href={routes.admin.albi()} className="btn btn-outline-secondary">{__("cancel")}</Link>
                    <Button
                        className="btn-primary"
                        disabled={!form.isDirty || form.processing}
                        onClick={() => form.patch(localizedRoute("admin.albo.update", {id: albo.id}))}
                    >
                        {__("save")}
                    </Button>
                </div>
            </div>
        </Form>
    </Page>;
};

export default Albo;
