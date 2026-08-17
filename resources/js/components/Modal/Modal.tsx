import {CSSProperties, FC, ReactNode} from "react";

export type ModalProps = {
    show: boolean;
    onClose?: () => void;
    style?: CSSProperties;
    children: ReactNode;
}

const Modal: FC<ModalProps> = ({show, onClose, children}: ModalProps) => {
    if (!show) return null;

    return (
        <div
            className="d-flex justify-content-center modal"
            style={{
                backgroundColor: "rgba(0,0,0,0.5)",
            }}
            onClick={(e) => {
                e.stopPropagation();
                const target = e.target as HTMLElement;
                if (target.classList.contains("modal") && onClose) {
                    onClose()
                }
            }}
        >
            {children}
        </div>);

}

export default Modal;
