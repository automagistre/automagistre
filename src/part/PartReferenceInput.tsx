import {AutocompleteInput, ReferenceInput, ReferenceInputProps} from 'react-admin'
import {Part} from '../types'
import PartNameField from './PartNameField'

const ContactReferenceInput = (inputProps: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>) => {
    const props = inputProps as ReferenceInputProps

    return (
        <ReferenceInput
            {...props}
            filterToQuery={searchText => ({'manufacturer#name@_ilike,name@_ilike,number@_ilike,comment@_ilike': searchText})}
        >
            <AutocompleteInput inputText={(record: Part) => record.number} optionText={<PartNameField/>}
                               matchSuggestion={() => true} source="name"/>
        </ReferenceInput>
    )
}

ContactReferenceInput.defaultProps = {
    label: 'Запчасть',
    source: 'part_id',
    reference: 'part',
}

export default ContactReferenceInput
