import {FC, ReactNode, useCallback} from 'react';
import './Register.css';
import classNames from "classnames";
import Page from "../components/Page/Page.tsx";
import Input from "../../../components/Input/Input.tsx";
import Button from "../../../components/Button/Button.tsx";
import {Link, useForm} from "@inertiajs/react";
import {faker} from "@faker-js/faker/locale/it";
import Form from "../../../components/Form/useForm.tsx";
import {useRoutes} from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";

type CredenzialiType = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}

const Register: FC = (): ReactNode => {

    const __ = useTranslations();
    const routes = useRoutes();

    const password = faker.internet.password();
    const name = faker.person.fullName();
    const email = name.replace(" ", ".") + "@example.com";
    const initialData = {
        name: name,
        email: email,
        password: password,
        password_confirmation: password,
    };

    const form = useForm<CredenzialiType>(initialData);
    const {post} = form;

    const onSubmit = useCallback(() => {
        post(route('register'), {});
    }, [post]);

    return (
        <Page browserTitle={__('register.page_title')}>
            <div className="container my-auto py-5">
                <div className="row justify-content-center">
                    <div className="col-md-7 col-lg-5">
                        <div className="card p-4">
                            <div className="text-center mb-4">
                                <i className="bi bi-person-plus display-4 text-primary"></i>
                                <h2 className="fw-bold mt-2">Registrati</h2>
                                <p className="text-muted">Crea il tuo account per usare il template base</p>
                            </div>
                            <Form form={form}>
                                <Input
                                    name={'name'}
                                    label={__('register.name')}
                                    icon={'bi-person'}/>
                                <Input
                                    name={'email'}
                                    label={__('register.email')}
                                    icon={'bi-envelope'}/>
                                <Input
                                    name={'password'}
                                    type={'password'}
                                    label={__('register.password')}
                                    icon={'bi-lock'}/>
                                <Input
                                    name={'password_confirmation'}
                                    type={'password'}
                                    label={__('register.confirm_password')}
                                    icon={'bi-lock-fill'}/>

                                <Button
                                    onClick={onSubmit}
                                    className={classNames(
                                        "btn-primary btn-lg",
                                        "d-block mx-auto w-100 w-md-50")}>
                                    {__('register.submit')}
                                </Button>
                            </Form>
                            <div className="text-center mt-4">
                                <p className="mb-0 text-muted">
                                    {__('register.already_have_account')}{' '}
                                    <Link href={routes.app.login()} className="text-primary fw-bold">
                                        {__('register.login')}
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

export default Register;
