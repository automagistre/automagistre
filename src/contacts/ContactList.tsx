import {BooleanField, Datagrid, FunctionField, List, ListProps,} from 'react-admin';
import {Contact, ContactOrganizationName, ContactPersonName} from "../types";
import ContactTypeReferenceInput from "./ContactTypeReferenceInput";
import ContactReferenceField from "./ContactTypeReferenceField";
import {PhoneNumberField} from "../phoneNumber";

const ContactName = (record: any) => {
    const contact: Contact = record;

    if (['NP', 'SP'].includes(contact.type)) {
        const name: ContactPersonName = contact.name;

        return `${name.lastname ?? ''} ${name.firstname ?? ''} ${name.middlename ?? ''}`
    } else {
        const name: ContactOrganizationName = contact.name;

        return name.name;
    }
}

const contactFilters = [
    <ContactTypeReferenceInput/>,
]

const ContactList = (props: ListProps) => {
    return (
        <List
            title="Контакты"
            perPage={10}
            sort={{field: 'updated_at', order: 'DESC'}}
            filters={contactFilters}
            {...props}
        >
            <Datagrid rowClick="edit">
                <ContactReferenceField/>
                <FunctionField
                    source="name"
                    label="Название"
                    render={ContactName}
                />
                <PhoneNumberField/>
                <BooleanField source="contractor" label="Подрядчик"/>
                <BooleanField source="supplier" label="Поставщик"/>
            </Datagrid>
        </List>
    );
};

export default ContactList;
