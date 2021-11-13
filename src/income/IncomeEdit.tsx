import {Edit, EditProps, SimpleForm, TextInput} from 'react-admin'
import {CommentInput} from '../comment'
import ContactReferenceField from '../contact/ContactReferenceField'
import SupplierReferenceInput from '../contact/SupplierReferenceInput'
import {Income} from '../types'

const IncomeTitle = ({record}: { record?: Income }) => record ?
    <span>Приход от <ContactReferenceField source="supplier_id" link={false}/></span> : null


const IncomeEdit = (props: EditProps) => {
    // TODO Allow edit only comment if income accrued
    return (
        <Edit {...props} title={<IncomeTitle/>}>
            <SimpleForm>
                <SupplierReferenceInput/>
                <TextInput
                    source="document"
                    label="Документ"
                />
                <CommentInput/>
            </SimpleForm>
        </Edit>
    )
}

export default IncomeEdit
