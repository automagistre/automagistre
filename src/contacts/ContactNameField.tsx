import {FieldProps, useRecordContext} from 'react-admin';
import {Contact, ContactOrganizationName, ContactPersonName} from "../types";

const ContactNameField = (props: FieldProps<Contact>) => {
    const contact: Contact = useRecordContext(props);

    if (contact.name.hasOwnProperty('firstname')) {
        const name: ContactPersonName = contact.name

        return <span>{name.lastname ?? ''} {name.firstname ?? ''} {name.middlename ?? ''}</span>
    } else if (contact.name.hasOwnProperty('name')) {
        const name: ContactOrganizationName = contact.name

        return <span>{name.name}</span>
    } else {
        console.error(`Unexpected name object: ${JSON.stringify(contact.name)}`)

        return <span>error</span>
    }
};

ContactNameField.defaultProps = {
    label: "Наименование",
    source: 'name',
    addLabel: true,
};

export default ContactNameField;
