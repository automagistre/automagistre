import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin';

interface Props {
    source?: string;
}

const LegalFormReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceField
        source="legal_form"
        reference="legal_form"
        {...props}
    >
        <TextField source="short_name"/>
    </ReferenceField>
);

LegalFormReferenceField.defaultProps = {
    label: "Правовая форма",
    addLabel: true,
    link: false,
};

export default LegalFormReferenceField;
