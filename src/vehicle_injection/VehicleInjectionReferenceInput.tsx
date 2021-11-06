import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin'

const VehicleInjectionReferenceInput = (
    props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceInput
        {...props}
        source="injection"
        reference="vehicle_injection"
    >
        <SelectInput optionText="name"/>
    </ReferenceInput>
)

VehicleInjectionReferenceInput.defaultProps = {
    source: 'injection',
    reference: 'vehicle_injection',
    label: 'Впрыск',
}

export default VehicleInjectionReferenceInput
