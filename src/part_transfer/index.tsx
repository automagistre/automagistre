import {EditGuesser} from 'react-admin'
import PartTransferList from './PartTransferList'

const partTransfers = {
    list: PartTransferList,
    create: EditGuesser,
    edit: EditGuesser,
}

export default partTransfers
