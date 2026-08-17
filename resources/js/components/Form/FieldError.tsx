import {FC, ReactNode} from "react";

type FieldErrorProps = {
    error: string;
};

const FieldError: FC<FieldErrorProps> = ({error}: FieldErrorProps): ReactNode =>
    (<div className={"small text-danger"}>
        {error}
    </div>);

export default FieldError;
