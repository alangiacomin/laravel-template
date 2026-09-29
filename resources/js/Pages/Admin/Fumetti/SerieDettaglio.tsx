import {FC, ReactNode} from "react";
import {Link, router, useForm, usePage} from "@inertiajs/react";
import Page from "../components/Page/Page.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Input from "../../../components/Input/Input.tsx";
import Button from "../../../components/Button/Button.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {localizedRoute} from "../../../localizedRoute.ts";
import type {SerieDetailData, SerieInputData, TestataOptionData} from "../../../types/generated";

type Props = { serie: SerieDetailData; testate: TestataOptionData[] };
const SerieDettaglio: FC = (): ReactNode => {
    const __ = useTranslations();
    const routes = useRoutes();
    const {serie, testate} = usePage<Props>().props;
    const form = useForm<SerieInputData>({titolo: serie.titolo, testata_id: String(serie.testata_id)});
    return <Page title={serie.titolo} browserTitle={serie.titolo} breadcrumb={[{
        name: __('admin.admin_panel'),
        href: routes.admin.dashboard()
    }, {name: __('admin.series'), href: routes.admin.serie()}, {name: serie.titolo, href: ''}]}><Form form={form}><Input
        name="titolo" label={__('admin.title')}/><label className="form-label">{__('admin.testata')}</label><select
        className="form-select mb-3" value={form.data.testata_id}
        onChange={e => form.setData('testata_id', e.target.value)}>{testate.map(t => <option key={t.id}
                                                                                             value={t.id}>{t.titolo}</option>)}</select>
        <div className="d-flex justify-content-between gap-2"><button type="button" className="btn btn-outline-danger"
            onClick={() => router.delete(localizedRoute('admin.serie.delete', {id: serie.id}), {
                onBefore: () => window.confirm(__('admin.delete_confirm')),
            })}>{__('delete')}</button><div className="d-flex gap-2"><Link href={routes.admin.serie()}
                                                                className="btn btn-outline-secondary">{__('cancel')}</Link><Button
            className="btn-primary" disabled={!form.isDirty || form.processing}
            onClick={() => form.patch(localizedRoute('admin.serie.update', {id: serie.id}))}>{__('save')}</Button></div></div>
    </Form>
        <hr/>
        <h4>{__('admin.albi')}</h4>
        <ul>{serie.albi.map(a => <li key={a.id}><Link href={routes.admin.albo(a.id)}>{a.titolo}</Link></li>)}</ul>
    </Page>
};
export default SerieDettaglio;
