import {faRecycle} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {EditGuesser} from 'react-admin'
import PartTransferList from './PartTransferList'

const partTransfers = {
    list: PartTransferList,
    create: EditGuesser,
    edit: EditGuesser,
    icon: <FontAwesomeIcon icon={faRecycle}/>,
}

export default partTransfers
