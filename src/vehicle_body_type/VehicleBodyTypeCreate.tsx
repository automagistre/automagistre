import {Create, CreateProps, required, SimpleForm, TextInput} from 'react-admin'

const VehicleBodyTypeCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm redirect="list">
                <TextInput
                    source="id"
                    validate={required()}
                    label="ID"
                />
                <TextInput
                    source="name"
                    validate={required()}
                    label="Название"
                />
            </SimpleForm>
        </Create>
    )
}

export default VehicleBodyTypeCreate
