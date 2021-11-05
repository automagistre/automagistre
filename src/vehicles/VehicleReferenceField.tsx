import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin'

interface Props {
    source?: string;
}

const VehicleReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceField
        label="Производитель"
        source="vehicle_id"
        reference="vehicles"
        {...props}
    >
        <TextField source="reference"/>
    </ReferenceField>
)

VehicleReferenceField.defaultProps = {
    source: 'vehicle_id',
    addLabel: true,
}

export default VehicleReferenceField
