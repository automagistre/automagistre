import {AutocompleteInput, ReferenceInput, ReferenceInputProps} from 'react-admin';

interface Props {
    source?: string;
}

const ContactReferenceInput = (
    props: Props &
        Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceInput
        {...props}
        source="contact_id"
        reference="contact"
        filterToQuery={searchText => ({"name,localized_name": searchText})}
    >
        <AutocompleteInput optionText="name" source="name"/>
    </ReferenceInput>
);

ContactReferenceInput.defaultProps = {
    label: "Счёт",
    source: 'contact_id',
    addLabel: true,
};

export default ContactReferenceInput;
