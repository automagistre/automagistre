import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin'

const VehicleDriveWheelReferenceInput = (
    props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceInput
        {...props}
        source="air_intake"
        reference="vehicle_drive_wheel"
    >
        <SelectInput optionText="name"/>
    </ReferenceInput>
)

VehicleDriveWheelReferenceInput.defaultProps = {
    source: 'air_intake',
    reference: 'vehicle_drive_wheel',
    label: 'Привод',
}

export default VehicleDriveWheelReferenceInput
