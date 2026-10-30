import { useForm } from "@inertiajs/react";
import type { FC, ReactNode } from "react";
import Button from "../../../components/Button/Button.tsx";
import useTranslations from "../../../hooks/useTranslations.tsx";
import Page from "../components/Page/Page.tsx";

const VerificationNotice: FC = (): ReactNode => {
  const __ = useTranslations();
  const form = useForm({});

  return (
    <Page browserTitle={__("verification.notice_title")}>
      <div className="container">
        <div className="row justify-content-center mt-5">
          <div className="col-md-6 text-center">
            <h2>{__("verification.notice_heading")}</h2>
            <p className="mt-3">{__("verification.notice_message")}</p>
            <p>{__("verification.notice_instruction")}</p>
            <Button
              type="button"
              className="btn-primary"
              disabled={form.processing}
              onClick={() => form.post(route("verification.send"))}
            >
              {__("verification.resend")}
            </Button>
          </div>
        </div>
      </div>
    </Page>
  );
};

export default VerificationNotice;
