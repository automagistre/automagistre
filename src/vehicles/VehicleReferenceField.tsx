import {ReferenceField, ReferenceFieldProps} from 'react-admin'
import VehicleField from './VehicleField'

interface Props {
    source?: string;
}

const VehicleReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceField
        label="Автомобиль"
        source="vehicle_id"
        reference="vehicle"
        {...props}
    >
        <VehicleField/>
    </ReferenceField>
)

VehicleReferenceField.defaultProps = {
    label: 'Автомобиль',
    source: 'vehicle_id',
    addLabel: true,
}

export default VehicleReferenceField
