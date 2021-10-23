import {Datagrid, List, ListProps, TextField,} from 'react-admin';

const TenantList = (props: ListProps) => {
    return (
        <List
            title="Сервисы"
            perPage={10}
            sort={{field: 'updated_at', order: 'DESC'}}

            {...props}
        >
            <Datagrid>
                <TextField
                    source="name"
                    label="Название"
                />
            </Datagrid>
        </List>
    );
};

export default TenantList;
