import {Datagrid, DateField, List, ListProps, TextField} from 'react-admin'
import ContactReferenceField from '../contacts/ContactReferenceField'
import VehicleReferenceField from '../vehicles/VehicleReferenceField'
import OrderStatusReferenceField from './OrderStatusReferenceField'

const OrderList = (props: ListProps) => (
    <List {...props}
          title="Заказы"
          sort={{field: 'number', order: 'DESC'}}
    >
        <Datagrid rowClick="edit">
            <TextField source="number" label="Номер"/>
            <OrderStatusReferenceField/>
            <DateField source="created_at" label="Создан" showTime={true}/>
            <VehicleReferenceField/>
            <ContactReferenceField source="contact_gave_id" label="Контакт"/>
        </Datagrid>
    </List>
)

export default OrderList
