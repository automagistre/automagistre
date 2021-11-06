import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin'

const VehicleFuelTypeReferenceInput = (
    props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceInput
        {...props}
        source="fuel_type"
        reference="vehicle_fuel_type"
    >
        <SelectInput optionText="name"/>
    </ReferenceInput>
)

VehicleFuelTypeReferenceInput.defaultProps = {
    source: 'fuel_type',
    reference: 'vehicle_fuel_type',
    label: 'Топливо',
}

export default VehicleFuelTypeReferenceInput
