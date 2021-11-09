import {faCar} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import VehicleCreate from './VehicleCreate'
import VehicleEdit from './VehicleEdit'
import VehicleList from './VehicleList'

const vehicles = {
    list: VehicleList,
    create: VehicleCreate,
    edit: VehicleEdit,
    icon: <FontAwesomeIcon icon={faCar}/>,
}

export default vehicles
