import {FC, ReactNode, useCallback} from "react";
import Button from "../../../components/Button/Button.tsx";
import Input from "../../../components/Input/Input.tsx";
import Form from "../../../components/Form/useForm.tsx";
import Modal from "../../../components/Modal/Modal.tsx";
import {useForm} from "@inertiajs/react";
import useTranslations from "../../../hooks/useTranslations.tsx";
import {UserData} from "../../../types/generated";

type EditProfileModalProps = {
    show: boolean;
    user: UserData;
    onClose: () => void;
};

type EditData = {
    name: string;
}

const EditProfileModal: FC<EditProfileModalProps> = ({
                                                         show,
                                                         onClose,
                                                         user,
                                                     }): ReactNode => {
    const initialData = {
        name: user.name,
    }
    const form = useForm<EditData>(initialData);
    const {patch} = form;
    const __ = useTranslations();

    const saveEdits = useCallback(() => {
        patch(
            route('user.update', {id: user.id}),
            {
                onSuccess: () => {
                    onClose();
                },
            });

    }, [patch, user.id, onClose]);

    const onSubmit = () => {
        saveEdits();
    }

    return (
        <Modal
            show={show}
            onClose={onClose}
        >
            <div className="modal-dialog"
                 style={{
                     minWidth: "500px",
                 }}>
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">{__('user.edit_profile')}</h5>
                    </div>
                    <div className="modal-body">
                        <Form form={form}>
                            <Input
                                name={'name'}
                                label={__('user.name')}
                                labelOnLeft
                            />
                        </Form>
                    </div>
                    <div className="modal-footer">
                        <Button className="btn-secondary" onClick={onClose}>
                            {__('cancel')}
                        </Button>
                        <Button className="btn-primary" disabled={!form.isDirty} onClick={onSubmit}>
                            {__('save')}
                        </Button>
                    </div>
                </div>
            </div>
        </Modal>
    );
};

export default EditProfileModal;
