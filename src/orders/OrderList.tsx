import {Datagrid, DateField, List, ListProps, ReferenceField, TextField} from 'react-admin'
import ContactReferenceField from '../contacts/ContactReferenceField'
import VehicleReferenceField from '../vehicles/VehicleReferenceField'


const OrderList = (props: ListProps) => (
    <List {...props}
          title="Работы"
          sort={{field: 'updated_at', order: 'DESC'}}
    >
        <Datagrid rowClick="edit">
            <TextField source="number"/>
            <ReferenceField source="status_id" reference="order_status"><TextField source="id"/></ReferenceField>
            <DateField source="created_at"/>
            <VehicleReferenceField/>
            <ContactReferenceField source="contact_gave_id" label="Контакт"/>
        </Datagrid>
    </List>
)

export default OrderList
