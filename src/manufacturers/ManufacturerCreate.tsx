import {Create, CreateProps, required, SimpleForm, TextInput,} from 'react-admin';

const ManufacturerCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm redirect="list">
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
        </Create>
    );
};

export default ManufacturerCreate;
