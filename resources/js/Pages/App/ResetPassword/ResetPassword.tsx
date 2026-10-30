import { useForm } from "@inertiajs/react";
import classNames from "classnames";
import { type FC, type ReactNode, useCallback } from "react";
import Button from "../../../components/Button/Button.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Input from "../../../components/Input/Input.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";
import Page from "../components/Page/Page.tsx";

type ResetPasswordProps = {
  token: string;
  email: string;
};

type ResetPasswordType = {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
};

const ResetPassword: FC<ResetPasswordProps> = ({
  token,
  email,
}: ResetPasswordProps): ReactNode => {
  const form = useForm<ResetPasswordType>({
    token,
    email,
    password: "",
    password_confirmation: "",
  });
  const __ = useTranslations();

  const onSubmit = useCallback(() => {
    form.post(route("password.update"));
  }, [form]);

  return (
    <Page browserTitle={__("password_reset.reset_title")}>
      <div className="container my-auto py-5">
        <div className="row justify-content-center">
          <div className="col-md-6 col-lg-4">
            <div className="card p-4">
              <div className="text-center mb-4">
                <i className="bi bi-shield-lock display-4 text-primary"></i>
                <h2 className="fw-bold mt-2">
                  {__("password_reset.reset_heading")}
                </h2>
                <p className="text-muted">
                  {__("password_reset.reset_subtitle")}
                </p>
              </div>
              <Form form={form}>
                <Input
                  name={"email"}
                  label={__("user.email")}
                  icon={"bi-envelope"}
                />
                <Input
                  name={"password"}
                  type={"password"}
                  label={__("user.password")}
                  icon={"bi-lock"}
                />
                <Input
                  name={"password_confirmation"}
                  type={"password"}
                  label={__("login.confirm_password")}
                  icon={"bi-lock-fill"}
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
                  {__("password_reset.reset_submit")}
                </Button>
              </Form>
            </div>
          </div>
        </div>
      </div>
    </Page>
  );
};

// noinspection JSUnusedGlobalSymbols
export default ResetPassword;
