import {FC, ReactNode, useCallback} from "react";
import Page from "../../Admin/components/Page/Page.tsx";
import {Link, router, useForm, usePage} from "@inertiajs/react";
import {PermissionEnum, Role as RoleType} from "../../../types/generated";
import Form from "../../../components/Form/useForm.tsx";
import Button from "../../../components/Button/Button.tsx";
import Input from "../../../components/Input/Input.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";

type RolePageProps = {
    role: RoleType,
}

const Role: FC = (): ReactNode => {

    const __ = useTranslations();
    const routes = useRoutes();
    const {role} = usePage<RolePageProps>().props;

    const initialData = {
        id: role.id,
        name: role.name,
        permissions: (() => {
            const permissionSet = new Set(role.permissions);

            return Object.fromEntries(
                Object.values(PermissionEnum).map((perm: string) => [
                    perm,
                    permissionSet.has(perm)
                ])
            ) as Record<PermissionEnum, boolean>;
        })()
    }
    const form = useForm(initialData);
    const {patch, isDirty, processing, reset} = form;

    const onSubmit = useCallback(() => {
        patch(route('admin.role.update', {id: role.id}), {
            onSuccess: (page) => {
                if (page.flash.error) {
                    reset();
                } else {
                    router.visit(routes.admin.roles());
                }
            }
        });
    }, [patch, reset, role.id, routes.admin]);

    const permSections = [...new Set(Object.keys(initialData.permissions).map((perm: string) => perm.split("_")[0]))];

    return (
        <Page
            title={`${__('admin.edit_role')}: ${role.name}`}
            subtitle={__('admin.edit_role_subtitle')}
            browserTitle={`${__('admin.edit_role')}: ${role.name}`}
            breadcrumb={[
                {name: __('admin.admin_panel'), href: routes.admin.dashboard()},
                {name: __('admin.roles'), href: routes.admin.roles()},
                {name: role.name, href: ''},
            ]}
        >

            <Form form={form}>

                <div className="row g-4">
                    {permSections.map((section) => (
                        <div key={section} className="col-12">
                            <div className="card">
                                <div className="card-header fw-semibold">
                                    {section}
                                </div>
                                <div className="card-body">
                                    <div className="row g-3">
                                        {Object.values(PermissionEnum)
                                            .filter((perm: string) => perm.startsWith(`${section}_`))
                                            .map((perm: string) => (
                                                <div className="col-12 col-sm-6 col-md-4 col-lg-3" key={`perm_${perm}`}>
                                                    <Input
                                                        type="checkbox"
                                                        name={`permissions.${perm}`}
                                                        label={perm}
                                                        labelOnLeft/>
                                                </div>
                                            ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="border-top pt-3 mt-5 d-flex justify-content-end gap-2">
                    <Link href={routes.admin.roles()} className="btn btn-outline-secondary">
                        {__('cancel')}
                    </Link>
                    <Button className="btn-primary"
                            disabled={!isDirty || processing}
                            onClick={onSubmit}
                    >
                        {__('save')}
                    </Button>
                </div>

            </Form>
        </Page>
    );
}

export default Role;
