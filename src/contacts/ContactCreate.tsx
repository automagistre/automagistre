import {BooleanInput, Create, CreateProps, required, SimpleForm, TextInput} from 'react-admin'
import LegalFormReferenceInput from '../legal_forms/LegalFormReferenceInput'
import {PhoneNumberInput} from '../phoneNumber'
import ContactNameInput from './ContactNameInput'

const ContactCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm redirect="show">
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
        </Create>
    )
}

export default ContactCreate
