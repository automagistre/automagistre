import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin';

interface Props {
    source?: string;
}

const ManufacturerReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceField
        source="manufacturer_id"
        reference="manufacturer"
        {...props}
    >
        <TextField source="name"/>
    </ReferenceField>
);

ManufacturerReferenceField.defaultProps = {
    label: "Производитель",
    source: 'manufacturer_id',
    addLabel: true,
};

export default ManufacturerReferenceField;
