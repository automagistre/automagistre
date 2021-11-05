import {FieldProps} from 'react-admin'
import {Link} from 'react-router-dom'
import {Vehicle} from '../types'

const VehicleRefField = ({record}: FieldProps<Vehicle>) =>
    record ? (
        <Link to={`vehicle/${record.id}`}>{record.name}</Link>
    ) : null

VehicleRefField.defaultProps = {
    source: 'id',
    label: 'Кузов',
}

export default VehicleRefField
