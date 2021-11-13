import {ReferenceField, ReferenceFieldProps} from 'react-admin'
import MoneyTransferReasonField from './MoneyTransferReasonField'


const MoneyTransferReasonReferenceField = (props: Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>) => (
    <ReferenceField
        {...props}
        source="reason"
        reference="money_transfer_reason"
    >
        <MoneyTransferReasonField/>
    </ReferenceField>
)

MoneyTransferReasonReferenceField.defaultProps = {
    source: 'reason',
    reference: 'money_transfer_reason',
    label: 'Причина',
    link: false,
    sortable: false,
}

export default MoneyTransferReasonReferenceField
