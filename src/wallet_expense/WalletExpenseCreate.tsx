import {Create, CreateProps, required, SimpleForm, TextInput} from 'react-admin'
import {CommentInput} from '../comment'
import WalletReferenceInput from '../wallets/WalletReferenceInput'

const WalletCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm redirect="list">
                <TextInput
                    source="name"
                    validate={required()}
                    label="Название"
                />
                <CommentInput/>
                <WalletReferenceInput/>
            </SimpleForm>
        </Create>
    )
}

export default WalletCreate
