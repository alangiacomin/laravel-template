import { Link, useForm } from "@inertiajs/react";
import classNames from "classnames";
import { type FC, type ReactNode, useCallback } from "react";
import Button from "../../../components/Button/Button.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Input from "../../../components/Input/Input.tsx";
import { useRoutes } from "../../../hooks/useRoutes.ts";
import useTranslations from "../../../hooks/useTranslations.tsx";
import Page from "../components/Page/Page.tsx";

type ForgotPasswordType = {
  email: string;
};

const ForgotPassword: FC = (): ReactNode => {
  const form = useForm<ForgotPasswordType>({ email: "" });
  const __ = useTranslations();
  const routes = useRoutes();

  const onSubmit = useCallback(() => {
    form.post(route("password.email"));
  }, [form]);

  return (
    <Page browserTitle={__("password_reset.forgot_title")}>
      <div className="container my-auto py-5">
        <div className="row justify-content-center">
          <div className="col-md-6 col-lg-4">
            <div className="card p-4">
              <div className="text-center mb-4">
                <i className="bi bi-key display-4 text-primary"></i>
                <h2 className="fw-bold mt-2">
                  {__("password_reset.forgot_heading")}
                </h2>
                <p className="text-muted">
                  {__("password_reset.forgot_subtitle")}
                </p>
              </div>
              <Form form={form}>
                <Input
                  name={"email"}
                  label={__("user.email")}
                  icon={"bi-envelope"}
                />
                <Button
                  type={"button"}
                  className={classNames(
                    "btn-primary btn-lg",
                    "d-block mx-auto w-100 w-md-50",
                  )}
                  disabled={form.processing}
                  onClick={onSubmit}
                >
                  {__("password_reset.send_link")}
                </Button>
              </Form>
              <div className="mt-4 text-center">
                <Link href={routes.app.login()}>
                  {__("password_reset.back_to_login")}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Page>
  );
};

// noinspection JSUnusedGlobalSymbols
export default ForgotPassword;
