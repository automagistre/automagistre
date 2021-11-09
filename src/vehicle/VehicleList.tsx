import {Datagrid, List, ListProps, TextField} from 'react-admin'
import VehicleBodyReferenceField from '../vehicle_body/VehicleBodyReferenceField'
import VehicleBodyTypeReferenceField from '../vehicle_body_type/VehicleBodyTypeReferenceField'

const VehicleList = (props: ListProps) => {
    return (
        <List
            title="Кузова"
            sort={{field: 'updated_at', order: 'DESC'}}

            {...props}
        >
            <Datagrid rowClick="edit">
                <TextField
                    source="identifier"
                    label="Идентификатор"
                />
                <VehicleBodyReferenceField source="body"/>
                <TextField source="year" label="Год"/>
                <VehicleBodyTypeReferenceField link={false}/>
                <TextField source="mileage" label="Пробег"/>
                <TextField source="legal_plate" label="Гос. Номер"/>
            </Datagrid>
        </List>
    )
}

export default VehicleList
