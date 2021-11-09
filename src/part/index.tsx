import {faCogs} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import PartCreate from './PartCreate'
import PartEdit from './PartEdit'
import PartList from './PartList'

const parts = {
    list: PartList,
    create: PartCreate,
    edit: PartEdit,
    icon: <FontAwesomeIcon icon={faCogs}/>,
}

export default parts
