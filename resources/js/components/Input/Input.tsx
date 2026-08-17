import {ChangeEvent, FC, InputHTMLAttributes, useCallback} from "react";
import classNames from "classnames";
import classnames from "classnames";
import FieldError from "../Form/FieldError.tsx";
import {useFormContext} from "../Form/useForm.tsx";

interface InputProps extends Omit<InputHTMLAttributes<HTMLInputElement>, "name" | "value" | "onChange"> {
    name: string;
    label?: string;
    icon?: string | null;
    labelOnLeft?: boolean;
}

const Input: FC<InputProps> = ({
                                   name,
                                   type = "text",
                                   label,
                                   icon = null,
                                   className,
                                   labelOnLeft = false,
                                   ...rest
                               }: InputProps) => {
    function getNestedValue(
        obj: unknown,
        path: string
    ): string | number | readonly string[] | undefined {
        const result = path.split(".").reduce<unknown>((acc, key) => {
            if (acc == null || typeof acc !== "object") {
                return undefined;
            }

            if (Array.isArray(acc)) {
                const index = Number(key);
                return Number.isNaN(index) ? undefined : acc[index];
            }

            return (acc as Record<string, unknown>)[key];
        }, obj);

        if (typeof result === "string"
            || typeof result === "number"
            || typeof result === "boolean"
            || (Array.isArray(result) && result.every(v => typeof v === "string"))
        ) {
            return result as string | number | readonly string[];
        }

        return undefined;
    }

    const {data, setData, errors} = useFormContext();
    const inputValue = getNestedValue(data, name);
    const hasError = !!errors[name];

    const isCheckbox = type === "checkbox";

    const handleChange = useCallback((e: ChangeEvent<HTMLInputElement>) => {
        setData(name, isCheckbox ? e.target.checked : e.target.value);
    }, [name, setData, isCheckbox]);


    return (
        <div className={classNames(
            "mb-3",
            {"d-flex align-items-center gap-2": labelOnLeft}
        )}>
            <label
                htmlFor={name}
                className={classNames(
                    "form-label",
                    {
                        "mb-0": labelOnLeft,
                    }
                )}
                style={labelOnLeft ? {flexShrink: 0} : undefined}
            >
                {label}
            </label>

            <div className={classNames(
                {
                    "flex-fill": labelOnLeft,
                }
            )}
                 style={labelOnLeft ? {minWidth: 0} : undefined}
            >
                <div className="input-group">
                    {icon && <span className="input-group-text">
                      <i className={classnames("bi", icon)}></i>
                    </span>}
                    <input
                        className={classNames(
                            {"form-control": type === "text" || type === "password"},
                            {"form-check-input": isCheckbox},
                            {"is-invalid": !!errors[name]},
                            className
                        )}
                        id={name}
                        name={name}
                        type={type}
                        {...rest}
                        checked={isCheckbox ? Boolean(inputValue) : undefined}
                        value={isCheckbox ? undefined : (inputValue ?? "")}
                        onChange={handleChange}
                    />
                </div>

                {hasError && <FieldError error={errors[name]}/>}
            </div>
        </div>
    );
};

export default Input;
