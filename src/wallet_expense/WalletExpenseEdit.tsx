import {Edit, EditProps, required, SimpleForm, TextInput} from 'react-admin'
import {CommentInput} from '../comment'
import {WalletExpense} from '../types'
import WalletReferenceInput from '../wallets/WalletReferenceInput'

interface WalletExpenseTitleProps {
    record?: WalletExpense;
}

const WalletExpenseTitle = ({record}: WalletExpenseTitleProps) => record ?
    <span>Расход {record.name}</span> : null

const WalletExpenseEdit = (props: EditProps) => {
    return (
        <Edit {...props} title={<WalletExpenseTitle/>}>
            <SimpleForm>
                <TextInput
                    source="name"
                    validate={required()}
                    label="Название"
                />
                <CommentInput/>
                <WalletReferenceInput/>
            </SimpleForm>
        </Edit>
    )
}

export default WalletExpenseEdit
