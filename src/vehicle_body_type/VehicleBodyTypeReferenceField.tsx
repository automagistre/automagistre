import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin'

const VehicleBodyTypeReferenceField = (
    props: Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceField
        source=""
        reference=""
        {...props}
    >
        <TextField source="name"/>
    </ReferenceField>
)

VehicleBodyTypeReferenceField.defaultProps = {
    label: 'Тип кузова',
    source: 'body_type',
    reference: 'vehicle_body_type',
    addLabel: true,
}

export default VehicleBodyTypeReferenceField
