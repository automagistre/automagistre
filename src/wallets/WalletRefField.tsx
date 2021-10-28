import {Link} from 'react-router-dom';
import {FieldProps} from 'react-admin';
import {Wallet} from '../types';

const WalletRefField = ({record}: FieldProps<Wallet>) =>
    record ? (
        <Link to={`wallet/${record.id}`}>{record.name}</Link>
    ) : null;

WalletRefField.defaultProps = {
    source: 'id',
    label: 'Счёт',
};

export default WalletRefField;
