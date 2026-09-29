import {FC, ReactNode} from "react";
import {Link, useForm, usePage} from "@inertiajs/react";
import Page from "../components/Page/Page.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Input from "../../../components/Input/Input.tsx";
import Button from "../../../components/Button/Button.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {localizedRoute} from "../../../localizedRoute.ts";
import type {SerieInputData, SerieSummaryData, TestataOptionData} from "../../../types/generated";

type Props = { serie: SerieSummaryData[]; testate: TestataOptionData[] };
const Serie: FC = (): ReactNode => {
    const __ = useTranslations();
    const routes = useRoutes();
    const {serie, testate} = usePage<Props>().props;
    const form = useForm<SerieInputData>({titolo: "", testata_id: ""});
    const gruppi = serie.reduce<{ testata: TestataOptionData; serie: SerieSummaryData[] }[]>((gruppi, s) => {
        const gruppo = gruppi[gruppi.length - 1];
        if (gruppo?.testata.id === s.testata.id) {
            gruppo.serie.push(s);
        } else {
            gruppi.push({testata: s.testata, serie: [s]});
        }
        return gruppi;
    }, []);
    return <Page title={__('admin.series')} subtitle={__('admin.series_subtitle')} browserTitle={__('admin.series')}
                 breadcrumb={[{
                     name: __('admin.admin_panel'),
                     href: routes.admin.dashboard()
                 }, {name: __('admin.series'), href: routes.admin.serie()}]}>
        <div className="card mb-4">
            <div className="card-body"><Form form={form}>
                <div className="row">
                    <div className="col-md-5"><label className="form-label">{__('admin.testata')}</label><select
                        className="form-select" value={form.data.testata_id}
                        onChange={e => form.setData('testata_id', e.target.value)}>
                        <option value="">--</option>
                        {testate.map(t => <option key={t.id} value={t.id}>{t.titolo}</option>)}</select></div>
                    <div className="col-md-5"><Input name="titolo" label={__('admin.title')}/></div>
                    <div className="col-md-2 d-flex align-items-end mb-3"><Button className="btn-primary"
                                                                                  onClick={() => form.post(localizedRoute('admin.serie.create'))}>{__('create')}</Button>
                    </div>
                </div>
            </Form></div>
        </div>
        <table className="table">
            <thead>
            <tr>
                <th>{__('admin.testata')}</th>
                <th></th>
            </tr>
            </thead>
            {testate.map(testata => <tbody key={testata.id} className="table-group-divider">
                <tr>
                <td>
                  <Link href={routes.admin.testata(testata.id)}>{testata.titolo}</Link>
                </td>
                <td>
                    <table className="table table-hover">
                        <thead>
                        <tr>
                            <th>{__('admin.title')}</th>
                            <th>{__('admin.albi')}</th>
                            <th></th>
                        </tr>
                        </thead>
                        {gruppi.filter(gruppo => gruppo.testata.id === testata.id).map(gruppo => <tbody key={gruppo.testata.id} className="table-group-divider">
                        {gruppo.serie.map(s => <tr key={s.id}>
                            <td><Link href={localizedRoute('admin.albi', {
                                testata_id: gruppo.testata.id,
                                serie_id: s.id,
                            })}>{s.titolo}</Link></td>
                            <td>{s.albi_count}</td>
                            <td className="text-end"><Link className="btn btn-sm btn-outline-primary"
                                                           href={routes.admin.serieItem(s.id)}>{__('edit')}</Link></td>
                        </tr>)}
                        </tbody>)}
                    </table></td>
            </tr>
            </tbody>)}
        </table>
    </Page>
};
export default Serie;
