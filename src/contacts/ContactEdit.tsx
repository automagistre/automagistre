import {
    BooleanInput,
    Edit,
    EditProps,
    NumberInput,
    ReferenceInput,
    SelectInput,
    SimpleForm,
    TextInput,
} from 'react-admin';
import {Contact} from '../types';
import {useFormState} from 'react-final-form'

interface ContactTitleProps {
    record?: Contact;
}

const ContactTitle = ({record}: ContactTitleProps) => record ? <span>{record.name.toString()}</span> : null;

const ContactForm = () => {
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
                    <ReferenceInput reference="contact_type" source="type">
                        <SelectInput/>
                    </ReferenceInput>
                    <BooleanInput source="contractor" label="Подрядчик"/>
                    <BooleanInput source="supplier" label="Поставщик"/>
                </>
                <ContactForm/>
                <>
                    <NumberInput source="telephone" type="tel" label="Телефон"/>
                    <TextInput source="email" type="email"/>
                </>
            </SimpleForm>
        </Edit>
    );
};

export default ContactEdit;
