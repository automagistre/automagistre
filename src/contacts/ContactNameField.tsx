import {FieldProps, useGetList, useRecordContext} from 'react-admin'
import {Contact, ContactOrganizationName, ContactPersonName} from '../types'


const ContactNameField = (props: FieldProps<Contact>) => {
    const contact: Contact = useRecordContext(props)
    const {data, loading} = useGetList('legal_form')

    if (!contact || loading) return <span>Загрузка...</span>

    const legalForm = data[contact.legal_form]

    if (legalForm.type === 'person') {
        const name: ContactPersonName = contact.name

        return <span>{name.lastname ?? ''} {name.firstname ?? ''} {name.middlename ?? ''}</span>
    } else if (legalForm.type === 'organization') {
        const name: ContactOrganizationName = contact.name

        return <span>{name.name ?? name.full_name}</span>
    } else {
        console.error(`legalForm "${contact.legal_form}" not found in legalForm list`)

        return <span>Ошибка</span>
    }
}

ContactNameField.defaultProps = {
    label: 'Наименование',
    source: 'name',
    addLabel: true,
}

export default ContactNameField
