import {Link} from 'react-router-dom';
import {FieldProps} from 'react-admin';
import {Part} from '../types';

const PartRefField = ({record}: FieldProps<Part>) =>
    record ? (
        <Link to={`part/${record.id}`}>{record.name}</Link>
    ) : null;

PartRefField.defaultProps = {
    source: 'id',
    label: 'Запчасть',
};

export default PartRefField;
