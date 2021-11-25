import {EditGuesser} from 'react-admin'
import OrderList from './OrderList'

const orders = {
    list: OrderList,
    create: EditGuesser,
    edit: EditGuesser,
}

export default orders
