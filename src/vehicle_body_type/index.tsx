import {faCar} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import VehicleBodyTypeCreate from './VehicleBodyTypeCreate'
import VehicleBodyTypeEdit from './VehicleBodyTypeEdit'
import VehicleBodyTypeList from './VehicleBodyTypeList'

const vehicleBodyTypes = {
    list: VehicleBodyTypeList,
    create: VehicleBodyTypeCreate,
    edit: VehicleBodyTypeEdit,
    icon: <FontAwesomeIcon icon={faCar}/>,
}

export default vehicleBodyTypes
