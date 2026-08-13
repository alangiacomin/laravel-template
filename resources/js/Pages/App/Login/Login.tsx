import {FC, ReactNode, useCallback} from "react";
import classNames from "classnames";
import Input from "../../../components/Input/Input.tsx";
import Button from "../../../components/Button/Button.tsx";
import Page from "../components/Page/Page.tsx";
import {Link, useForm} from "@inertiajs/react";
import Form from "../../../components/Form/useForm.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";

type CredenzialiType = {
    email: string;
    password: string;
}

const Login: FC = (): ReactNode => {

    const initialData = {
        email: '',
        password: 'password',
    };
    const form = useForm<CredenzialiType>(initialData);
    const __ = useTranslations();
    const routes = useRoutes();

    const fakeSubmit = (email: string, password: string) => {
        form.setData({
            ...form.data,
            email: email,
            password: password,
        });
    }

    const onSubmit = useCallback(() => {
        form.post(route('login'));
    }, [form]);

    return (
        <Page browserTitle={__('login')}>
            <div className="container my-auto py-5">
                <div className={"row justify-content-center"}>
                    <div className="col-md-6 col-lg-4">
                        <div className="card p-4">
                            <div className="text-center mb-4">
                                <i className="bi bi-person-circle display-4 text-primary"></i>
                                <h2 className="fw-bold mt-2">Accedi</h2>
                                <p className="text-muted">Accedi per continuare a usare il template base</p>
                            </div>
                            <Form form={form}>
                                <Input
                                    name={'email'}
                                    label={__('user.email')}
                                    icon={'bi-envelope'}/>
                                <Input
                                    name={'password'}
                                    type={'password'}
                                    label={__('user.password')}
                                    icon={'bi-lock'}/>

                                <Button
                                    type={"button"}
                                    className={classNames(
                                        "btn-primary btn-lg",
                                        "d-block mx-auto w-100 w-md-50")}
                                    onClick={onSubmit}
                                >
                                    {__('login')}
                                </Button>
                            </Form>
                            {/* Fake login - begin */}
                            {import.meta.env.DEV && (
                                <div className="d-block mx-auto mt-3 text-center">
                                    <Button
                                        onClick={() => {
                                            fakeSubmit("admin@example.com", "password123");
                                        }}>
                                        Admin
                                    </Button>
                                    <Button
                                        onClick={() => {
                                            fakeSubmit("editor@example.com", "password123");
                                        }}
                                    >
                                        Editor
                                    </Button>
                                    <Button
                                        onClick={() => {
                                            fakeSubmit("user@example.com", "password123");
                                        }}
                                    >
                                        User
                                    </Button>
                                </div>
                            )}
                            {/* Fake login - end */}
                            <div className="mt-4 text-center">
                                <p className="mb-0 text-muted">
                                    {__('login.new_user')}{' '}
                                    <Link href={routes.app.register()} className="text-primary fw-bold">
                                        {__('login.create_account')}
                                    </Link>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Page>
    );
};

// noinspection JSUnusedGlobalSymbols
export default Login;
