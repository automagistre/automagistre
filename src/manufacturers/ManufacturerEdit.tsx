import {Edit, EditProps, required, SimpleForm, TextInput,} from 'react-admin';
import {Manufacturer} from '../types';

interface ManufacturerTitleProps {
    record?: Manufacturer;
}

const ManufacturerTitle = ({record}: ManufacturerTitleProps) => record ?
    <span>Производитель {record.name}</span> : null;

const ManufacturerEdit = (props: EditProps) => {
    return (
        <Edit {...props} title={<ManufacturerTitle/>}>
            <SimpleForm>
                <TextInput
                    source="name"
                    validate={required()}
                    label="Название"
                />
                <TextInput
                    source="localized_name"
                    label="Название на русском"
                />
            </SimpleForm>
        </Edit>
    );
};

export default ManufacturerEdit;
