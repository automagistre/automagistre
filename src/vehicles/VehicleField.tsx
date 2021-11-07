import {TextField} from 'react-admin'
import {Vehicle} from '../types'
import VehicleBodyReferenceField from '../vehicle_bodies/VehicleBodyReferenceField'

const VehicleBodyNameField = ({record}: { record?: Vehicle }) => (
    <>
        <VehicleBodyReferenceField record={record} link={false}/>
        {' '}
        |
        {' '}
        <TextField source="legal_plate" record={record}/>
    </>
)

VehicleBodyNameField.defaultProps = {
    source: 'vehicle_body',
    addLabel: true,
}

export default VehicleBodyNameField
