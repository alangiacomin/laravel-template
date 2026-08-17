import {ButtonHTMLAttributes, FC, MouseEvent, ReactNode} from "react";
import classNames from "classnames";

interface ButtonProps extends Omit<ButtonHTMLAttributes<HTMLButtonElement>, "type"> {
    type?: ButtonHTMLAttributes<HTMLButtonElement>["type"];
    children?: ReactNode;
}

const Button: FC<ButtonProps> = ({type = "button", onClick, children, className, ...rest}: ButtonProps): ReactNode => {
    const handleClick = (e: MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();
        onClick?.(e);
    };

    return (
        <button
            type={type}
            className={classNames("btn", className)}
            onClick={handleClick}
            {...rest}>
            {children}
        </button>);
};

export default Button;
