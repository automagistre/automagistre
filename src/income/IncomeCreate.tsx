import {Create, CreateProps, required, SimpleForm, TextInput} from 'react-admin'
import {CommentInput} from '../comment'
import SupplierReferenceInput from '../contact/SupplierReferenceInput'


const IncomeCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm>
                <SupplierReferenceInput
                    validate={required()}
                />
                <TextInput
                    source="document"
                    label="Документ"
                />
                <CommentInput/>
            </SimpleForm>
        </Create>
    )
}

export default IncomeCreate
