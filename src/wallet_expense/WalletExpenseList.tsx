import {Datagrid, List, ListProps, TextField} from 'react-admin'
import {CommentField} from '../comment'
import WalletReferenceField from '../wallets/WalletReferenceField'

const WalletExpenseList = (props: ListProps) => {
    return (
        <List
            title="Расходы"
            sort={{field: 'updated_at', order: 'DESC'}}

            {...props}
        >
            <Datagrid rowClick="edit">
                <TextField
                    source="name"
                    label="Название"
                />
                <CommentField/>
                <WalletReferenceField label="Счёт по умолчанию"/>
            </Datagrid>
        </List>
    )
}

export default WalletExpenseList
