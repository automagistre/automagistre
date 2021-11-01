import {BooleanInput, Edit, EditProps, SimpleForm, TextInput,} from 'react-admin';
import {Contact} from '../types';
import {useFormState} from 'react-final-form'
import ContactTypeReferenceInput from "./ContactTypeReferenceInput";
import {PhoneNumberInput} from "../phoneNumber";

interface ContactTitleProps {
    record?: Contact;
}

const ContactTitle = ({record}: ContactTitleProps) => record ? <span>{record.name.toString()}</span> : null;

export const ContactForm = () => {
    const {values} = useFormState();

    return (
        ['NP', 'SP'].includes(values.type) ? <>
            <TextInput source="name.lastname" label="Фамилия"/>
            <TextInput source="name.firstname" label="Имя"/>
            <TextInput source="name.middlename" label="Отчество"/>
        </> : <>
            <TextInput source="name.name" label="Краткое название"/>
            <TextInput source="name.full_name" label="Полное название" fullWidth/>
        </>
    )
}

const ContactEdit = (props: EditProps) => {
    return (
        <Edit {...props} title={<ContactTitle/>}>
            <SimpleForm>
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
        </Edit>
    );
};

export default ContactEdit;
