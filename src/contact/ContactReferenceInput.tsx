import {AutocompleteInput, ReferenceInput, ReferenceInputProps} from 'react-admin'
import {Contact} from '../types'
import ContactNameField from './ContactNameField'

const ContactReferenceInput = (props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>) => (
    <ReferenceInput
        {...props}
        source="contact_id"
        reference="contact"
        filterToQuery={searchText => ({'telephone': searchText})}
    >
        <AutocompleteInput inputText={(record: Contact) => record.telephone} optionText={<ContactNameField/>}
                           matchSuggestion={() => true} source="name"/>
    </ReferenceInput>
)

ContactReferenceInput.defaultProps = {
    label: 'Контакт',
    source: 'contact_id',
    addLabel: false,
}

export default ContactReferenceInput
