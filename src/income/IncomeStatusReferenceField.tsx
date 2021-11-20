import {ReferenceField, ReferenceFieldProps} from 'react-admin'
import IncomeStatusField from './IncomeStatusField'


const IncomeStatusReferenceField = (props: Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>) => (
    <ReferenceField
        {...props}
        source="status_id"
        reference="income_status"
    >
        <IncomeStatusField/>
    </ReferenceField>
)

IncomeStatusReferenceField.defaultProps = {
    source: 'status_id',
    reference: 'income_status',
    label: 'Статус',
    link: false,
    sortable: false,
}

export default IncomeStatusReferenceField
