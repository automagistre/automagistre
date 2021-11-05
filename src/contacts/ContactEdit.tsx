import {BooleanInput, Edit, EditProps, required, SimpleForm, TextInput} from 'react-admin'
import LegalFormReferenceInput from '../legal_forms/LegalFormReferenceInput'
import {PhoneNumberInput} from '../phoneNumber'
import {Contact} from '../types'
import {ContactAside} from './ContactAside'
import ContactNameInput from './ContactNameInput'

interface ContactTitleProps {
    record?: Contact;
}

const ContactTitle = ({record}: ContactTitleProps) => record ? <span>{record.name.toString()}</span> : null

const ContactEdit = (props: EditProps) => {
    return (
        <Edit {...props} aside={<ContactAside link="show"/>} title={<ContactTitle/>} actions={false}>
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
        </Edit>
    )
}

export default ContactEdit
