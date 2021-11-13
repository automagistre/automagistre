import {ReferenceField, ReferenceFieldProps} from 'react-admin'
import OrderField from './OrderField'


const OrderReferenceField = (props: Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>) => (
    <ReferenceField
        source="order_id"
        reference="order"
        {...props}
    >
        <OrderField/>
    </ReferenceField>
)

OrderReferenceField.defaultProps = {
    source: 'order_id',
    reference: 'order',
    label: 'Заказ',
    link: false,
    sortable: false,
}

export default OrderReferenceField
