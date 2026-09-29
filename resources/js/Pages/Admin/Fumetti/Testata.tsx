import {FC, ReactNode} from "react";
import {Link, router, useForm, usePage} from "@inertiajs/react";
import Page from "../components/Page/Page.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Input from "../../../components/Input/Input.tsx";
import Button from "../../../components/Button/Button.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {localizedRoute} from "../../../localizedRoute.ts";
import type {TestataDetailData, TestataInputData} from "../../../types/generated";

type Props = { testata: TestataDetailData };
const Testata: FC = (): ReactNode => {
    const __ = useTranslations();
    const routes = useRoutes();
    const {testata} = usePage<Props>().props;
    const form = useForm<TestataInputData>({titolo: testata.titolo});
    return <Page title={testata.titolo} browserTitle={testata.titolo} breadcrumb={[{
        name: __('admin.admin_panel'),
        href: routes.admin.dashboard()
    }, {name: __('admin.testate'), href: routes.admin.testate()}, {name: testata.titolo, href: ''}]}><Form
        form={form}><Input name="titolo" label={__('admin.title')}/>
        <div className="d-flex justify-content-between gap-2">
            <button type="button" className="btn btn-outline-danger"
                    onClick={() => router.delete(localizedRoute('admin.testata.delete', {id: testata.id}), {
                        onBefore: () => window.confirm(__('admin.delete_confirm')),
                    })}>{__('delete')}</button>
            <div className="d-flex gap-2"><Link href={routes.admin.testate()}
                                                className="btn btn-outline-secondary">{__('cancel')}</Link><Button
                className="btn-primary" disabled={!form.isDirty || form.processing}
                onClick={() => form.patch(localizedRoute('admin.testata.update', {id: testata.id}))}>{__('save')}</Button>
            </div>
        </div>
    </Form>
        <hr/>
        <h4>{__('admin.albi')}</h4>
        <ul>{testata.albi.map(item => <li key={item.id}><Link
            href={routes.admin.serieItem(item.id)}>{item.titolo}</Link></li>)}</ul>
    </Page>
};
export default Testata;
