import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin'

interface Props {
    source?: string;
}

const LegalFormTypeReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceField
        source="type"
        reference="legal_form_type"
        {...props}
    >
        <TextField source="name"/>
    </ReferenceField>
)

LegalFormTypeReferenceField.defaultProps = {
    label: 'Тип правовой формы',
    source: 'type',
    addLabel: true,
    link: false,
}

export default LegalFormTypeReferenceField
