import {TextField} from 'react-admin'
import ManufacturerReferenceField from '../manufacturers/ManufacturerReferenceField'
import {VehicleBody} from '../types'

const VehicleBodyNameField = ({record}: { record?: VehicleBody }) => (
    <>

        <ManufacturerReferenceField record={record} link={false}/>
        &nbsp;
        <TextField source="name" record={record}/>
        &nbsp;
        <TextField source="case_name" record={record}/>
    </>
)

VehicleBodyNameField.defaultProps = {
    source: 'vehicle_body',
    addLabel: true,
}

export default VehicleBodyNameField
