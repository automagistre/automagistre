import {BooleanField, Datagrid, List, ListProps, TextField} from 'react-admin'
import ManufacturerReferenceField from '../manufacturer/ManufacturerReferenceField'
import UnitReferenceField from './UnitReferenceField'

const PartList = (props: ListProps) => {
    return (
        <List
            title="Запчасти"
            sort={{field: 'updated_at', order: 'DESC'}}

            {...props}
        >
            <Datagrid rowClick="edit">
                <TextField
                    source="name"
                    label="Название"
                />
                <ManufacturerReferenceField/>
                <TextField
                    source="number"
                    label="Номер"
                />
                <BooleanField
                    source="universal"
                    label="Номер"
                />
                <UnitReferenceField/>
            </Datagrid>
        </List>
    )
}

export default PartList
