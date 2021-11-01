import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin';

interface Props {
    source?: string;
}

const ContactReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceField
        source="type"
        reference="contact_type"
        {...props}
    >
        <TextField source="name.short"/>
    </ReferenceField>
);

ContactReferenceField.defaultProps = {
    label: "Правовая форма",
    source: 'type',
    addLabel: true,
    link: false,
};

export default ContactReferenceField;
