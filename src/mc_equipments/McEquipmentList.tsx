import {BooleanField, Datagrid, List, ListProps, NumberField, TextField} from 'react-admin'
import VehicleBodyReferenceField from '../vehicle_bodies/VehicleBodyReferenceField'
import VehicleDriveWheelReferenceField from '../vehicle_drive_wheel/VehicleDriveWheelReferenceField'
import VehicleTransmissionReferenceField from '../vehicle_transmission/VehicleTransmissionReferenceField'

const McWorkList = (props: ListProps) => (
    <List {...props}
          title="Комплектации"
          sort={{field: 'updated_at', order: 'DESC'}}
    >
        <Datagrid rowClick="edit">
            <TextField source="engine_name" label="Двигатель"/>
            <VehicleBodyReferenceField/>
            <TextField source="engine_capacity" label="Объём"/>
            <VehicleTransmissionReferenceField/>
            <VehicleDriveWheelReferenceField/>
            <NumberField source="period" label="Период"/>
            <BooleanField source="is_published" label="Опубликовано?"/>
        </Datagrid>
    </List>
)

export default McWorkList
