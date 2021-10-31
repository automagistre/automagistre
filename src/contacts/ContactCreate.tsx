import {BooleanInput, Create, CreateProps, required, SimpleForm, TextInput,} from 'react-admin';

const ContactCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm redirect="list">
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
        </Create>
    );
};

export default ContactCreate;
