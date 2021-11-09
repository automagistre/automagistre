import {faHandHoldingUsd} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import WalletExpenseCreate from './WalletExpenseCreate'
import WalletExpenseEdit from './WalletExpenseEdit'
import WalletExpenseList from './WalletExpenseList'

const walletExpense = {
    list: WalletExpenseList,
    create: WalletExpenseCreate,
    edit: WalletExpenseEdit,
    icon: <FontAwesomeIcon icon={faHandHoldingUsd}/>,
}

export default walletExpense
