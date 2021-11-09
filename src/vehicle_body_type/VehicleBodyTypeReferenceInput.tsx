import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin'

const VehicleBodyTypeReferenceInput = (props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>) => (
    <ReferenceInput
        {...props}
        source="body_type"
        reference="vehicle_body_type"
    >
        <SelectInput optionText="name"/>
    </ReferenceInput>
)

VehicleBodyTypeReferenceInput.defaultProps = {
    source: 'body_type',
    reference: 'vehicle_body_type',
    label: 'Тип кузова',
}

export default VehicleBodyTypeReferenceInput
