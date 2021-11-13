import {linkToRecord} from 'react-admin'
import {Link} from 'react-router-dom'
import {Order} from '../types'


const OrderField = ({record}: { record?: Order }) => {
    if (!record) return null
    const orderShowPage = linkToRecord('/order', record.id, 'show')
    return <span>Заказ №<Link to={orderShowPage}>{record.number}</Link></span>
}

export default OrderField
