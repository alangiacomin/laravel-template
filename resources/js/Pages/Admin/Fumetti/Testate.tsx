import {FC, ReactNode} from "react";
import {Link, useForm, usePage} from "@inertiajs/react";
import Page from "../components/Page/Page.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Input from "../../../components/Input/Input.tsx";
import Button from "../../../components/Button/Button.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {localizedRoute} from "../../../localizedRoute.ts";
import type {TestataInputData, TestataSummaryData} from "../../../types/generated";

type Props = { testate: TestataSummaryData[] };

const Testate: FC = (): ReactNode => {
    const __ = useTranslations();
    const routes = useRoutes();
    const {testate} = usePage<Props>().props;
    const form = useForm<TestataInputData>({titolo: ""});
    return <Page title={__('admin.testate')} subtitle={__('admin.testate_subtitle')} browserTitle={__('admin.testate')}
                 breadcrumb={[{
                     name: __('admin.admin_panel'),
                     href: routes.admin.dashboard()
                 }, {name: __('admin.testate'), href: routes.admin.testate()}]}>
        <div className="card mb-4">
            <div className="card-body"><Form form={form}>
                <div className="row align-items-end">
                    <div className="col-md-8"><Input name="titolo" label={__('admin.title')}/></div>
                    <div className="col-md-4 mb-3"><Button className="btn-primary"
                                                           onClick={() => form.post(localizedRoute('admin.testate.create'))}>{__('create')}</Button>
                    </div>
                </div>
            </Form></div>
        </div>
        <div className="table-responsive">
            <table className="table">
                <thead>
                <tr>
                    <th>{__('admin.title')}</th>
                    <th>{__('admin.albi')}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>{testate.map(item => <tr key={item.id}>
                    <td>{item.titolo}</td>
                    <td>
                        <span className="d-block">{item.albi_count}</span>
                    </td>
                    <td className="text-end"><Link className="btn btn-sm btn-outline-primary"
                                                   href={routes.admin.testata(item.id)}>{__('edit')}</Link></td>
                </tr>)}</tbody>
            </table>
        </div>
    </Page>;
};

export default Testate;
