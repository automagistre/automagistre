import {FieldProps, useRecordContext} from 'react-admin'
import {Link} from 'react-router-dom'
import ContactReferenceField from '../contact/ContactReferenceField'
import {Income} from '../types'

const IncomeRefField = (props: FieldProps<Income>) => {
    const record = useRecordContext(props)

    return record ? (
        <Link to={`income/${record.id}`}>Приход <ContactReferenceField source="supplier_id"/></Link>
    ) : null
}

IncomeRefField.defaultProps = {
    source: 'id',
    label: 'Приход',
}

export default IncomeRefField
