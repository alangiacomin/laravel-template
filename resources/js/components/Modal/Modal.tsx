import {
  type CSSProperties,
  type FC,
  type ReactNode,
  useEffect,
  useRef,
} from "react";

export type ModalProps = {
  show: boolean;
  onClose?: () => void;
  style?: CSSProperties;
  labelledBy?: string;
  children: ReactNode;
};

const Modal: FC<ModalProps> = ({
  show,
  onClose,
  labelledBy,
  children,
}: ModalProps) => {
  const ref = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    if (!show) {
      return;
    }
    const previouslyFocused = document.activeElement as HTMLElement | null;
    ref.current?.focus();
    return () => {
      previouslyFocused?.focus?.();
    };
  }, [show]);

  if (!show) return null;

  return (
    <div
      ref={ref}
      role="dialog"
      aria-modal="true"
      aria-labelledby={labelledBy}
      tabIndex={-1}
      className="d-flex justify-content-center align-items-center modal overflow-y-auto p-2"
      style={{
        backgroundColor: "rgba(0,0,0,0.5)",
      }}
      onKeyDown={(e) => {
        if (e.key === "Escape" && onClose) {
          e.stopPropagation();
          onClose();
        }
      }}
      onClick={(e) => {
        e.stopPropagation();
        const target = e.target as HTMLElement;
        if (target.classList.contains("modal") && onClose) {
          onClose();
        }
      }}
    >
      {children}
    </div>
  );
};

export default Modal;
