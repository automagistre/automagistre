import {Datagrid, List, ListProps, TextField} from 'react-admin'

const VehicleBodyTypeList = (props: ListProps) => {
    return (
        <List
            title="Типы кузовов"
            perPage={10}
            sort={{field: 'updated_at', order: 'DESC'}}

            {...props}
        >
            <Datagrid rowClick="edit">
                <TextField
                    source="id"
                    label="ID"
                />
                <TextField
                    source="name"
                    label="Название"
                />
            </Datagrid>
        </List>
    )
}

export default VehicleBodyTypeList
