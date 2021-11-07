import {TextField} from 'react-admin'
import {Vehicle} from '../types'
import VehicleBodyReferenceField from '../vehicle_bodies/VehicleBodyReferenceField'
import VehicleDriveWheelReferenceField from '../vehicle_drive_wheel/VehicleDriveWheelReferenceField'
import VehicleTransmissionReferenceField from '../vehicle_transmission/VehicleTransmissionReferenceField'

const VehicleBodyNameField = ({record}: { record?: Vehicle }) => (
    <>
        <VehicleBodyReferenceField record={record} link={false}/>
        &nbsp;
        |
        &nbsp;
        <TextField source="engine_name" record={record}/>
        &nbsp;
        <TextField source="engine_capacity" record={record}/>
        &nbsp;
        <TextField source="engine_capacity" record={record}/>
        &nbsp;
        <VehicleTransmissionReferenceField/>
        &nbsp;
        <VehicleDriveWheelReferenceField/>
        &nbsp;
        |
        &nbsp;
        <TextField source="legal_plate" record={record}/>
    </>
)

VehicleBodyNameField.defaultProps = {
    source: 'vehicle_body',
    addLabel: true,
}

export default VehicleBodyNameField
