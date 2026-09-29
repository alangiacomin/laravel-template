import {FC, ReactNode, useState} from "react";
import {Link, router, useForm, usePage} from "@inertiajs/react";
import Page from "../components/Page/Page.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Input from "../../../components/Input/Input.tsx";
import Button from "../../../components/Button/Button.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {localizedRoute} from "../../../localizedRoute.ts";
import type {AlboInputData, AlboSummaryData, TestataOptionData} from "../../../types/generated";

type Props = {
    albi: AlboSummaryData[];
    serie: SerieWithTestataData[];
    testate: TestataOptionData[];
    filters: {testata_id: number | null; serie_id: number | null};
};
const Albi: FC = (): ReactNode => {
    const __ = useTranslations();
    const routes = useRoutes();
    const {albi, serie, testate, filters} = usePage<Props>().props;
    const [creazioneTestataId, setCreazioneTestataId] = useState("");
    const [creazioneSerieId, setCreazioneSerieId] = useState("");
    const form = useForm<AlboInputData>({
        titolo: "",
        serie: []
    });
    const serieDisponibili = serie.filter(item => item.testata.id === Number(creazioneTestataId));
    const seriePerFiltro = filters.testata_id === null ? [] : serie;
    const filtraAlbi = (testataId: number | null, serieId: number | null) => {
        router.get(routes.admin.albi(), {
            testata_id: testataId ?? "",
            serie_id: serieId ?? "",
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };
    const righe = albi.flatMap(albo => albo.serie.length
        ? albo.serie.map(associazione => ({id: `${albo.id}-${associazione.id}`, albo, associazione}))
        : [{id: `${albo.id}-senza-serie`, albo, associazione: null}])
        .sort((a, b) => {
            const testataCompare = (a.associazione?.testata.titolo ?? "").localeCompare(b.associazione?.testata.titolo ?? "");
            if (testataCompare !== 0) {
                return testataCompare;
            }

            const gruppoCompare = (a.associazione?.numero_gruppo ?? Number.MAX_SAFE_INTEGER)
                - (b.associazione?.numero_gruppo ?? Number.MAX_SAFE_INTEGER);
            if (gruppoCompare !== 0) {
                return gruppoCompare;
            }

            return (a.associazione?.numero ?? Number.MAX_SAFE_INTEGER)
                - (b.associazione?.numero ?? Number.MAX_SAFE_INTEGER);
        });
    const testataIdRiga = (index: number) => righe[index].associazione?.testata.id ?? null;
    return <Page title={__('admin.albi')} subtitle={__('admin.albi_subtitle')} browserTitle={__('admin.albi')}
                 breadcrumb={[{name: __('admin.admin_panel'), href: routes.admin.dashboard()}, {
                     name: __('admin.albi'),
                     href: routes.admin.albi()
                 }]}>
        <div className="card mb-4">
            <div className="card-body"><Form form={form}>
                <div className="row align-items-end">
                    <div className="col-md-3">
                        <label className="form-label">{__('admin.testata')}</label>
                        <select
                            className="form-select"
                            value={creazioneTestataId}
                            onChange={event => {
                                setCreazioneTestataId(event.target.value);
                                setCreazioneSerieId("");
                                form.setData("serie", []);
                            }}
                        >
                            <option value="">--</option>
                            {testate.map(item => <option key={item.id} value={item.id}>{item.titolo}</option>)}
                        </select>
                    </div>
                    <div className="col-md-3">
                        <label className="form-label">{__('admin.series')}</label>
                        <select
                            className="form-select"
                            value={creazioneSerieId}
                            disabled={!creazioneTestataId}
                            onChange={event => {
                                const selectedId = event.target.value;
                                setCreazioneSerieId(selectedId);
                                form.setData("serie", selectedId ? [{
                                    serie_id: Number(selectedId),
                                    numero: 1,
                                    numero_gruppo: null,
                                    data_pubblicazione: null,
                                }] : []);
                            }}
                        >
                            <option value="">--</option>
                            {serieDisponibili.map(item => <option key={item.id} value={item.id}>{item.titolo}</option>)}
                        </select>
                    </div>
                    <div className="col-md-3"><Input name="titolo" label={__('admin.title')}/></div>
                    <div className="col-md-2">
                        <Input
                            name="serie.0.numero"
                            type="number"
                            label={__('admin.number')}
                            value={form.data.serie[0]?.numero ?? ""}
                            disabled={!creazioneSerieId}
                            onChange={event => form.setData("serie", form.data.serie.map(item => ({
                                ...item,
                                numero: Number(event.target.value),
                            })))}
                        />
                    </div>
                    <div className="col-md-1 mb-3"><Button
                        className="btn-primary"
                        disabled={!creazioneSerieId || form.processing}
                        onClick={() => form.post(localizedRoute('admin.albi.create'))}
                    >{__('create')}</Button>
                    </div>
                </div>
            </Form></div>
        </div>
        <div className="row mb-3">
            <div className="col-md-4">
                <label className="form-label">{__('admin.testata')}</label>
                <select
                    className="form-select"
                    value={filters.testata_id ?? ""}
                    onChange={event => filtraAlbi(event.target.value ? Number(event.target.value) : null, null)}
                >
                    <option value="">--</option>
                    {testate.map(item => <option key={item.id} value={item.id}>{item.titolo}</option>)}
                </select>
            </div>
            <div className="col-md-4">
                <label className="form-label">{__('admin.series')}</label>
                <select
                    className="form-select"
                    value={filters.serie_id ?? ""}
                    disabled={filters.testata_id === null}
                    onChange={event => filtraAlbi(
                        filters.testata_id,
                        event.target.value ? Number(event.target.value) : null,
                    )}
                >
                    <option value="">--</option>
                    {seriePerFiltro.map(item => <option key={item.id} value={item.id}>{item.titolo}</option>)}
                </select>
            </div>
            <div className="col-md-2 d-flex align-items-end mb-3">
                <button type="button" className="btn btn-outline-secondary"
                        onClick={() => filtraAlbi(null, null)}>{__('admin.clear_filters')}</button>
            </div>
        </div>
        <table className="table">
            <thead>
            <tr>
                <th>{__('admin.testata')}</th>
                <th>{__('admin.title')}</th>
                <th>{__('admin.series')}</th>
                <th>{__('admin.number')}</th>
                <th>{__('admin.publication_date')}</th>
            </tr>
            </thead>
            <tbody>{righe.map((riga, index) => {
                const testataId = testataIdRiga(index);
                const primaRigaTestata = index === 0 || testataIdRiga(index - 1) !== testataId;
                const rowSpan = primaRigaTestata
                    ? righe.slice(index).findIndex(row => (row.associazione?.testata.id ?? null) !== testataId)
                    : 0;

                return <tr key={riga.id}>
                    {primaRigaTestata && <td rowSpan={rowSpan === -1 ? righe.length - index : rowSpan}>
                        {riga.associazione
                            ? <Link href={routes.admin.testata(riga.associazione.testata.id)}>{riga.associazione.testata.titolo}</Link>
                            : "—"}
                    </td>}
                    <td><Link href={routes.admin.albo(riga.albo.id)}>{riga.albo.titolo}</Link></td>
                    <td>{riga.associazione
                        ? <Link href={routes.admin.serieItem(riga.associazione.id)}>{riga.associazione.titolo}</Link>
                        : "—"}</td>
                    <td>{riga.associazione
                        ? riga.associazione.numero_gruppo !== null
                            ? `${riga.associazione.numero_gruppo}-${riga.associazione.numero}`
                            : riga.associazione.numero
                        : "—"}</td>
                    <td>{riga.associazione?.data_pubblicazione ?? "—"}</td>
                </tr>;
            })}</tbody>
        </table>
    </Page>
};
export default Albi;
