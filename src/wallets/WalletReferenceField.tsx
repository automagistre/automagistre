import {ReferenceField, ReferenceFieldProps, TextField} from 'react-admin'

interface Props {
    source?: string;
}

const WalletReferenceField = (
    props: Props &
        Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceField
        source="wallet_id"
        reference="wallet"
        {...props}
    >
        <TextField source="name"/>
    </ReferenceField>
)

WalletReferenceField.defaultProps = {
    label: 'Счёт',
    source: 'wallet_id',
    addLabel: true,
}

export default WalletReferenceField
