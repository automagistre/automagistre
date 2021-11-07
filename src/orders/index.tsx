import {faFileInvoiceDollar} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {EditGuesser} from 'react-admin'
import OrderList from './OrderList'

const orders = {
    list: OrderList,
    create: EditGuesser,
    edit: EditGuesser,
    icon: <FontAwesomeIcon icon={faFileInvoiceDollar}/>,
}

export default orders
