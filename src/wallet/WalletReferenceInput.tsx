import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin'

interface Props {
    source?: string;
}

const WalletReferenceInput = (
    props: Props &
        Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceInput
        {...props}
        source="wallet_id"
        reference="wallet"
        filterToQuery={searchText => ({'name': searchText})}
    >
        <SelectInput optionText="name" source="name"/>
    </ReferenceInput>
)

WalletReferenceInput.defaultProps = {
    label: 'Счёт',
    source: 'wallet_id',
}

export default WalletReferenceInput
