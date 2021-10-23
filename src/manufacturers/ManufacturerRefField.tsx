import {Link} from 'react-router-dom';
import {FieldProps} from 'react-admin';
import {Manufacturer} from '../types';

const ManufacturerRefField = ({record}: FieldProps<Manufacturer>) =>
    record ? (
        <Link to={`manufacturer/${record.id}`}>{record.name}</Link>
    ) : null;

ManufacturerRefField.defaultProps = {
    source: 'id',
    label: 'Производитель',
};

export default ManufacturerRefField;
