import {BooleanField, Datagrid, List, ListProps} from 'react-admin'
import LegalFormReferenceField from '../legal_form/LegalFormReferenceField'
import LegalFormReferenceInput from '../legal_form/LegalFormReferenceInput'
import {PhoneNumberField} from '../phoneNumber'
import ContactNameField from './ContactNameField'

const contactFilters = [
    <LegalFormReferenceInput/>,
]

const ContactList = (props: ListProps) => {
    return (
        <List
            title="Контакты"
            sort={{field: 'updated_at', order: 'DESC'}}
            filters={contactFilters}
            {...props}
        >
            <Datagrid rowClick="edit">
                <LegalFormReferenceField/>
                <ContactNameField/>
                <PhoneNumberField/>
                <BooleanField source="contractor" label="Подрядчик"/>
                <BooleanField source="supplier" label="Поставщик"/>
            </Datagrid>
        </List>
    )
}

export default ContactList
