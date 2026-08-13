import {FC, ReactNode, useCallback, useMemo} from "react";
import Page from "../../Admin/components/Page/Page.tsx";
import {Link, router, useForm, usePage} from "@inertiajs/react";
import {AdminUserData, GateEnum, RoleEnum} from "../../../types/generated";
import UserTitle from "./UserTitle.tsx";
import UserDati from "./UserDati.tsx";
import UserRoles from "./UserRoles.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Button from "../../../components/Button/Button.tsx";
import useAuth from "../../../hooks/useAuth.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import {useToast} from "../../../components/Toast/useToast.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";
import useInertia, {PageWithFlash} from "../../../hooks/useInertia.ts";

type UserPageProps = {
    user: AdminUserData,
}

const User: FC = (): ReactNode => {

    const __ = useTranslations();
    const routes = useRoutes();
    const {user} = usePage<UserPageProps>().props;
    const currentUser = useAuth();
    const {inertiaRouter} = useInertia();
    const toast = useToast();

    const canEdit = currentUser && currentUser.can(GateEnum.USER_EDIT) && !user.isBanned;
    const canManage = currentUser && currentUser.can(GateEnum.USER_MANAGE);

    const initialData = useMemo(() => ({
        id: user.id,
        name: user.name,
        roles: (() => {
            const roleSet = new Set(user.roles);

            return Object.fromEntries(
                Object.values(RoleEnum).map((role: string) => [
                    role,
                    roleSet.has(role)
                ])
            ) as Record<RoleEnum, boolean>;
        })()
    }), [user.id, user.name, user.roles]);

    const form = useForm(initialData);
    const {isDirty, processing} = form;

    const onSubmit = useCallback(() => {
        form.patch(route('admin.user.update', {id: user.id}), {
            onSuccess: () => {
                router.visit(routes.admin.users());
            },
        });
    }, [form, routes.admin, user.id]);

    const onBlocca = useCallback(() => {
        inertiaRouter.patch(
            route('admin.user.blocca', {id: user.id}),
            {},
            {
                onSuccess: (page: PageWithFlash<string>) => {
                    if (page.props.flash?.success) {
                        toast.success(page.props.flash.success);
                    }
                },
            }
        );
    }, [inertiaRouter, toast, user.id]);

    const onSblocca = useCallback(() => {
        inertiaRouter.patch(
            route('admin.user.sblocca', {id: user.id}),
            {},
            {
                onSuccess: (page: PageWithFlash<string>) => {
                    if (page.props.flash?.success) {
                        toast.success(page.props.flash.success);
                    }
                },
            }
        );
    }, [inertiaRouter, toast, user.id]);

    return (
        <Page
            title={`${__('admin.edit_user')}: ${user.name}`}
            subtitle={__('admin.edit_user_subtitle')}
            browserTitle={`${__('admin.edit_user')}: ${user.name}`}
            breadcrumb={[
                {name: __('admin.admin_panel'), href: routes.admin.dashboard()},
                {name: __('admin.users'), href: routes.admin.users()},
                {name: user.name, href: ''},
            ]}
        >
            <UserTitle user={user}/>

            <Form form={form}>
                <UserDati readOnly={!canEdit}/>
                <UserRoles readOnly={!canEdit}/>

                <div className="border-top pt-3 mt-5 d-flex align-items-center">
                    <div className="d-flex gap-2">
                        {canManage && !user.isBanned && (
                            <Button
                                className={"btn-outline-danger"}
                                onClick={onBlocca}
                            >
                                {__('admin.ban')}
                            </Button>
                        )}
                        {canManage && user.isBanned && (
                            <Button
                                className={"btn-outline-success"}
                                onClick={onSblocca}
                            >
                                {__('admin.unban')}
                            </Button>
                        )}
                    </div>
                    <div className="d-flex gap-2 ms-auto">
                        {user.isBanned && (
                            <Link href={routes.admin.users()} className="btn btn-outline-secondary">
                                {__('back')}
                            </Link>
                        )}
                        {canEdit && (
                            <>
                                <Link href={routes.admin.users()} className="btn btn-outline-secondary">
                                    {__('cancel')}
                                </Link>
                                <Button className="btn-primary"
                                        disabled={!isDirty || processing}
                                        onClick={onSubmit}
                                >
                                    {__('save')}
                                </Button>
                            </>
                        )}
                    </div>
                </div>

            </Form>

        </Page>
    );
}

export default User;
