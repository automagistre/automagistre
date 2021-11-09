import {ReferenceField, ReferenceFieldProps} from 'react-admin'
import OrderStatusField from './OrderStatusField'


const OrderStatusReferenceField = (props: Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>) => (
    <ReferenceField
        {...props}
        source="status_id"
        reference="order_status"
    >
        <OrderStatusField/>
    </ReferenceField>
)

OrderStatusReferenceField.defaultProps = {
    source: 'status_id',
    reference: 'order_status',
    label: 'Статус',
    link: false,
    sortable: false,
}

export default OrderStatusReferenceField
