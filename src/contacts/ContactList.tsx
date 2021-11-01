import {
    BooleanField,
    Datagrid,
    FunctionField,
    List,
    ListProps,
    ReferenceField,
    ReferenceInput,
    SelectInput,
    TextField,
} from 'react-admin';
import {Contact, ContactOrganizationName, ContactPersonName} from "../types";

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
    <ReferenceInput reference="contact_type" source="type" label="Правовая форма">
        <SelectInput/>
    </ReferenceInput>,
]

const ContactList = (props: ListProps) => {
    return (
        <List
            title="Счета"
            perPage={10}
            sort={{field: 'updated_at', order: 'DESC'}}
            filters={contactFilters}
            {...props}
        >
            <Datagrid rowClick="edit">
                <ReferenceField reference="contact_type" source="type" label="Форма" link={false}>
                    <TextField source="name"/>
                </ReferenceField>
                <FunctionField
                    source="name"
                    label="Название"
                    render={ContactName}
                />
                <TextField
                    source="telephone"
                    label="Телефон"
                />
                <BooleanField source="contractor" label="Подрядчик"/>
                <BooleanField source="supplier" label="Поставщик"/>
            </Datagrid>
        </List>
    );
};

export default ContactList;
