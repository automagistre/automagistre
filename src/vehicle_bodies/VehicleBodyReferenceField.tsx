import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin'

interface Props {
    source?: string;
}

const VehicleBodyReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceField
        label="Кузов"
        source="vehicle_body"
        reference="vehicle_body"
        {...props}
    >
        <TextField source="name"/>
    </ReferenceField>
)

VehicleBodyReferenceField.defaultProps = {
    source: 'vehicle_body',
    addLabel: true,
}

export default VehicleBodyReferenceField
