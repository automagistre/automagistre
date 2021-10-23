import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin';

interface Props {
    source?: string;
}

const PartReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceField
        source="part_id"
        reference="part"
        {...props}
    >
        <TextField source="name"/>
    </ReferenceField>
);

PartReferenceField.defaultProps = {
    label: 'Запчасть',
    source: 'part_id',
    addLabel: true,
};

export default PartReferenceField;
