import {Datagrid, List, ListProps, TextField} from 'react-admin'
import ManufacturerReferenceField from '../manufacturers/ManufacturerReferenceField'

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
                    source="name"
                    label="Название"
                />
                <ManufacturerReferenceField/>
                <TextField
                    source="case_name"
                    label="Кузов"
                />
                <TextField
                    source="year_from"
                    label="Начало производства"
                />
                <TextField
                    source="year_till"
                    label="Конец производства"
                />
            </Datagrid>
        </List>
    )
}

export default VehicleList
