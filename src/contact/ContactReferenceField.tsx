import {ReferenceField, ReferenceFieldProps} from 'react-admin'
import ContactNameField from './ContactNameField'

interface Props {
    source?: string;
}

const ContactReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceField
        source="contact_id"
        reference="contact"
        {...props}
    >
        <ContactNameField/>
    </ReferenceField>
)

ContactReferenceField.defaultProps = {
    label: 'Контакт',
    source: 'contact_id',
    addLabel: true,
}

export default ContactReferenceField
