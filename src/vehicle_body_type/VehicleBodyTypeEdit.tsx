import {Edit, EditProps, required, SimpleForm, TextInput} from 'react-admin'
import {VehicleBodyType} from '../types'

interface VehicleBodyTypeTitleProps {
    record?: VehicleBodyType;
}

const VehicleBodyTypeTitle = ({record}: VehicleBodyTypeTitleProps) => record ?
    <span>Тип Кузов {record.name}</span> : null

const VehicleBodyTypeEdit = (props: EditProps) => {
    return (
        <Edit {...props} title={<VehicleBodyTypeTitle/>}>
            <SimpleForm>
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
        </Edit>
    )
}

export default VehicleBodyTypeEdit
