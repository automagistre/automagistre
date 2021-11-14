import {Edit, EditProps, required, SimpleForm, useRedirect} from 'react-admin'
import {CommentInput} from '../comment'
import {MoneyInput} from '../money'
import PartReferenceInput from '../part/PartReferenceInput'
import {QuantityInput} from '../part_transfer/QuantityField'
import {IncomePart} from '../types'

const IncomePartEdit = (props: EditProps) => {
    const redirect = useRedirect()

    const onSuccess = ({data}: { data: IncomePart }) => {
        redirect(`/income/${data.income_id}/parts`)
    }
    return (
        <Edit {...props} onSuccess={onSuccess} mutationMode="pessimistic">
            <SimpleForm>
                <PartReferenceInput validate={required()}/>
                <QuantityInput validate={required()}/>
                <MoneyInput validate={required()}/>
                <CommentInput/>
            </SimpleForm>
        </Edit>
    )
}

export default IncomePartEdit
