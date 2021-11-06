import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin'

interface Props {
    source?: string;
}

const VehicleAirIntakeReferenceInput = (
    props: Props &
        Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceInput
        {...props}
        source="air_intake"
        reference="vehicle_air_intake"
    >
        <SelectInput optionText="name"/>
    </ReferenceInput>
)

VehicleAirIntakeReferenceInput.defaultProps = {
    source: 'air_intake',
    reference: 'vehicle_air_intake',
    label: 'Наддув',
}

export default VehicleAirIntakeReferenceInput
