import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin'

const VehicleDriveWheelReferenceInput = (
    props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceInput
        {...props}
        source="drive_wheel"
        reference="vehicle_drive_wheel"
    >
        <SelectInput optionText="name"/>
    </ReferenceInput>
)

VehicleDriveWheelReferenceInput.defaultProps = {
    source: 'drive_wheel',
    reference: 'vehicle_drive_wheel',
    label: 'Привод',
}

export default VehicleDriveWheelReferenceInput
