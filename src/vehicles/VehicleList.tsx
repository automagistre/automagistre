import {Datagrid, List, ListProps, TextField} from 'react-admin'
import VehicleBodyReferenceField from '../vehicle_bodies/VehicleBodyReferenceField'

const VehicleList = (props: ListProps) => {
    return (
        <List
            title="Кузова"
            perPage={10}
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
                <TextField source="body_type" label="Кузов"/>
                <TextField source="mileage" label="Пробег"/>
                <TextField source="legal_plate" label="Гос. Номер"/>
            </Datagrid>
        </List>
    )
}

export default VehicleList
