import {BooleanField, Datagrid, List, ListProps, TextField,} from 'react-admin';
import {MoneyField} from "../money";

const WalletList = (props: ListProps) => {
    return (
        <List
            title="Счета"
            perPage={10}
            sort={{field: 'updated_at', order: 'DESC'}}

            {...props}
        >
            <Datagrid rowClick="edit">
                <TextField
                    source="name"
                    label="Название"
                />
                <MoneyField
                    source="balance"
                    label="Баланс"
                />
                <BooleanField source="use_in_income" label="Использовать для оплаты приходов"/>
                <BooleanField source="use_in_order" label="Использовать для начислений по заказам"/>
                <BooleanField source="show_in_layout" label="Показывать в шапке"/>
                <BooleanField source="default_in_manual_transaction" label="По умолчанию в ручной проводке"/>
            </Datagrid>
        </List>
    );
};

export default WalletList;
