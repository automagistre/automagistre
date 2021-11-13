import {faCoins} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {EditGuesser} from 'react-admin'
import MoneyTransferList from './MoneyTransferList'

const moneyTransfers = {
    list: MoneyTransferList,
    create: EditGuesser,
    edit: EditGuesser,
    icon: <FontAwesomeIcon icon={faCoins}/>,
}

export default moneyTransfers
