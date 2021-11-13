import {Datagrid, DateField, List, ListProps, TextField} from 'react-admin'
import MoneyTransferReasonReferenceField from './MoneyTransferReasonReferenceField'

const MoneyTransferList = (props: ListProps) => (
    <List {...props}
          title="Заказы"
          sort={{field: 'updated_at', order: 'DESC'}}
    >
        <Datagrid rowClick="edit">
            <TextField source="target" label="Получатель"/>
            <MoneyTransferReasonReferenceField/>
            {/* TODO generate description */}
            <DateField source="created_at" label="Создан" showTime={true}/>
        </Datagrid>
    </List>
)

export default MoneyTransferList
