import {FieldProps} from 'react-admin'
import {Link} from 'react-router-dom'
import {Contact} from '../types'

const ContactRefField = ({record}: FieldProps<Contact>) =>
    record ? (
        <Link to={`contact/${record.id}`}>{record.name}</Link>
    ) : null

ContactRefField.defaultProps = {
    source: 'id',
    label: 'Счёт',
}

export default ContactRefField
