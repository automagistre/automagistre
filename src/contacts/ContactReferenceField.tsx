import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin';

interface Props {
    source?: string;
}

const ContactReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceField
        source="contact_id"
        reference="contact"
        {...props}
    >
        <TextField source="name"/>
    </ReferenceField>
);

ContactReferenceField.defaultProps = {
    label: "Счёт",
    source: 'contact_id',
    addLabel: true,
};

export default ContactReferenceField;
