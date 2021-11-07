import {Vehicle} from '../types'
import VehicleBodyReferenceField from '../vehicle_bodies/VehicleBodyReferenceField'

const VehicleBodyNameField = ({record}: { record?: Vehicle }) => {
    if (!record) return null

    return (
        <>
            <VehicleBodyReferenceField record={record} link={false}/>
            {record.legal_plate && ` | ${record.legal_plate}`}
        </>
    )
}

VehicleBodyNameField.defaultProps = {
    source: 'vehicle_body',
    addLabel: true,
}

export default VehicleBodyNameField
