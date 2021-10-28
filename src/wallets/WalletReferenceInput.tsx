import {AutocompleteInput, ReferenceInput, ReferenceInputProps} from 'react-admin';

interface Props {
    source?: string;
}

const WalletReferenceInput = (
    props: Props &
        Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceInput
        {...props}
        source="wallet_id"
        reference="wallet"
        filterToQuery={searchText => ({"name,localized_name": searchText})}
    >
        <AutocompleteInput optionText="name" source="name"/>
    </ReferenceInput>
);

WalletReferenceInput.defaultProps = {
    label: "Счёт",
    source: 'wallet_id',
    addLabel: true,
};

export default WalletReferenceInput;
