import {AutocompleteInput, ReferenceInput, ReferenceInputProps} from 'react-admin';

interface Props {
    source?: string;
}

const ManufacturerReferenceInput = (
    props: Props &
        Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceInput
        {...props}
        source="manufacturer_id"
        reference="manufacturer"
        filterToQuery={searchText => ({"name,localized_name": searchText})}
    >
        <AutocompleteInput optionText="name" source="name"/>
    </ReferenceInput>
);

ManufacturerReferenceInput.defaultProps = {
    label: "Производитель",
    source: 'manufacturer_id',
    addLabel: true,
};

export default ManufacturerReferenceInput;
