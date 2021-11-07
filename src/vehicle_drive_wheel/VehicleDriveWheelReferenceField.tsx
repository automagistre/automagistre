import {ReferenceField, ReferenceInputProps, TextField} from 'react-admin'

const VehicleDriveWheelReferenceField = (
    props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceField
        {...props}
        source="drive_wheel"
        reference="vehicle_drive_wheel"
    >
        <TextField source="name"/>
    </ReferenceField>
)

VehicleDriveWheelReferenceField.defaultProps = {
    source: 'drive_wheel',
    reference: 'vehicle_drive_wheel',
    label: 'Привод',
}

export default VehicleDriveWheelReferenceField
