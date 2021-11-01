import {BooleanInput, Create, CreateProps, SimpleForm, TextInput,} from 'react-admin';
import ContactTypeReferenceInput from "./ContactTypeReferenceInput";
import {ContactForm} from "./ContactEdit";
import {PhoneNumberInput} from "../phoneNumber";

const ContactCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm redirect="list">
                <>
                    <ContactTypeReferenceInput/>
                    <BooleanInput source="contractor" label="Подрядчик"/>
                    <BooleanInput source="supplier" label="Поставщик"/>
                </>
                <ContactForm/>
                <>
                    <PhoneNumberInput/>
                    <TextInput source="email" type="email"/>
                </>
            </SimpleForm>
        </Create>
    );
};

export default ContactCreate;
