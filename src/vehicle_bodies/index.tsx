import {faCar} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import VehicleBodyCreate from './VehicleBodyCreate'
import VehicleBodyEdit from './VehicleBodyEdit'
import VehicleBodyList from './VehicleBodyList'

const vehicleBodies = {
    list: VehicleBodyList,
    create: VehicleBodyCreate,
    edit: VehicleBodyEdit,
    icon: <FontAwesomeIcon icon={faCar}/>,
}

export default vehicleBodies
