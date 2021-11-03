import {BooleanInput, Edit, EditProps, required, SimpleForm, TextInput,} from 'react-admin';
import {Contact} from '../types';
import LegalFormReferenceInput from "../legal_forms/LegalFormReferenceInput";
import {PhoneNumberInput} from "../phoneNumber";
import ContactNameInput from "./ContactNameInput";

interface ContactTitleProps {
    record?: Contact;
}

const ContactTitle = ({record}: ContactTitleProps) => record ? <span>{record.name.toString()}</span> : null;

const ContactEdit = (props: EditProps) => {
    return (
        <Edit {...props} title={<ContactTitle/>}>
            <SimpleForm>
                <LegalFormReferenceInput
                    validate={required()}
                />
                <ContactNameInput/>
                <>
                    <PhoneNumberInput/>
                    <TextInput source="email" type="email"/>
                </>
                <BooleanInput source="contractor" label="Подрядчик"/>
                <BooleanInput source="supplier" label="Поставщик"/>
            </SimpleForm>
        </Edit>
    );
};

export default ContactEdit;
