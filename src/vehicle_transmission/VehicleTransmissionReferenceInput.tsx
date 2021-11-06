import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin'

const VehicleTransmissionReferenceInput = (
    props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceInput
        {...props}
        source="transmission"
        reference="vehicle_transmission"
    >
        <SelectInput optionText="name"/>
    </ReferenceInput>
)

VehicleTransmissionReferenceInput.defaultProps = {
    source: 'transmission',
    reference: 'vehicle_transmission',
    label: 'Трансмиссия',
}

export default VehicleTransmissionReferenceInput
