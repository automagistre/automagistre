import {Datagrid, List, ListProps, TextField,} from 'react-admin';

const ManufacturerList = (props: ListProps) => {
    return (
        <List
            title="Производители"
            perPage={10}
            sort={{field: 'updated_at', order: 'DESC'}}

            {...props}
        >
            <Datagrid rowClick="edit">
                <TextField
                    source="name"
                    label="Название"
                />
                <TextField
                    source="localized_name"
                    label="Название на русском"
                />
            </Datagrid>
        </List>
    );
};

export default ManufacturerList;
