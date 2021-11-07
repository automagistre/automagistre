import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin'

const VehicleTransmissionReferenceField = (
    props: Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceField
        {...props}
        source="transmission"
        reference="vehicle_transmission"
    >
        <TextField source="name"/>
    </ReferenceField>
)

VehicleTransmissionReferenceField.defaultProps = {
    source: 'transmission',
    reference: 'vehicle_transmission',
    label: 'Трансмиссия',
}

export default VehicleTransmissionReferenceField
