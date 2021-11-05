import {faIndustry} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import ManufacturerCreate from './ManufacturerCreate'
import ManufacturerEdit from './ManufacturerEdit'
import ManufacturerList from './ManufacturerList'

const manufacturers = {
    list: ManufacturerList,
    create: ManufacturerCreate,
    edit: ManufacturerEdit,
    icon: <FontAwesomeIcon icon={faIndustry}/>,
}

export default manufacturers
