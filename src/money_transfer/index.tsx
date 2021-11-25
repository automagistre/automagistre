import {EditGuesser} from 'react-admin'
import MoneyTransferList from './MoneyTransferList'

const moneyTransfers = {
    list: MoneyTransferList,
    create: EditGuesser,
    edit: EditGuesser,
}

export default moneyTransfers
