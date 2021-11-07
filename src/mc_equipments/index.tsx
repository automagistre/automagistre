import {faMap} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {EditGuesser} from 'react-admin'
import McEquipmentList from './McEquipmentList'

const mcEquipments = {
    list: McEquipmentList,
    create: EditGuesser,
    edit: EditGuesser,
    icon: <FontAwesomeIcon icon={faMap}/>,
}

export default mcEquipments
