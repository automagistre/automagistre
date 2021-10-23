import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin';

interface Props {
    source?: string;
}

const UnitReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceField
        source="unit"
        reference="unit"
        {...props}
    >
        <TextField source="name"/>
    </ReferenceField>
);

UnitReferenceField.defaultProps = {
    label: 'Единица измерения',
    source: 'unit',
    addLabel: true,
    link: false,
};

export default UnitReferenceField;
