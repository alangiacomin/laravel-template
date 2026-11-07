import type { FC, ReactNode } from "react";

type FieldErrorProps = {
  error: string;
  id?: string;
};

const FieldError: FC<FieldErrorProps> = ({
  error,
  id,
}: FieldErrorProps): ReactNode => (
  <div id={id} role="alert" className={"small text-danger"}>
    {error}
  </div>
);

export default FieldError;
