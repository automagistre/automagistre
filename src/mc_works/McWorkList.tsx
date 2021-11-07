import {Datagrid, List, ListProps, TextField} from 'react-admin'
import {MoneyField} from '../money'

const McWorkList = (props: ListProps) => (
    <List {...props}
          title="Работы"
          sort={{field: 'updated_at', order: 'DESC'}}
    >
        <Datagrid rowClick="edit">
            <TextField source="name" label="Название"/>
            <TextField source="comment" label="Комментарий"/>
            <MoneyField source="price_amount"/>
        </Datagrid>
    </List>
)

export default McWorkList
