import {ReferenceField, ReferenceFieldProps} from 'react-admin'
import VehicleBodyNameField from './VehicleBodyNameField'

const VehicleBodyReferenceField = (props: Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>) => (
    <ReferenceField
        label="Кузов"
        source="vehicle_body_id"
        reference="vehicle_body"
        {...props}
    >
        <VehicleBodyNameField/>
    </ReferenceField>
)

VehicleBodyReferenceField.defaultProps = {
    source: 'vehicle_body_id',
    label: 'Кузов',
}

export default VehicleBodyReferenceField
