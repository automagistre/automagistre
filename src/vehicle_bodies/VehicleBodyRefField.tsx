import {FieldProps} from 'react-admin'
import {Link} from 'react-router-dom'
import {VehicleBody} from '../types'

const VehicleBodyRefField = ({record}: FieldProps<VehicleBody>) =>
    record ? (
        <Link to={`vehicle_body/${record.id}`}>{record.name}</Link>
    ) : null

VehicleBodyRefField.defaultProps = {
    source: 'id',
    label: 'Кузов',
}

export default VehicleBodyRefField
