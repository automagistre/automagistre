import {BooleanInput, Edit, EditProps, required, SimpleForm, TextInput,} from 'react-admin';
import {Wallet} from '../types';

interface WalletTitleProps {
    record?: Wallet;
}

const WalletTitle = ({record}: WalletTitleProps) => record ?
    <span>Счёт {record.name}</span> : null;

const WalletEdit = (props: EditProps) => {
    return (
        <Edit {...props} title={<WalletTitle/>}>
            <SimpleForm>
                <TextInput
                    source="name"
                    validate={required()}
                    label="Название"
                />
                <BooleanInput source="use_in_income" label="Использовать для оплаты приходов"/>
                <BooleanInput source="use_in_order" label="Использовать для начислений по заказам"/>
                <BooleanInput source="show_in_layout" label="Показывать в шапке"/>
                <BooleanInput source="default_in_manual_transaction" label="По умолчанию в ручной проводке"/>
            </SimpleForm>
        </Edit>
    );
};

export default WalletEdit;
