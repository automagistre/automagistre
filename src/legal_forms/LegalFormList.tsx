import {Datagrid, List, ListProps, TextField} from 'react-admin'
import LegalFormTypeReferenceField from './LegalFormTypeReferenceField'

const LegalFormList = (props: ListProps) => {
    return (
        <List
            title="Правовые формы"
            perPage={10}
            sort={{field: 'updated_at', order: 'DESC'}}
            {...props}
        >
            <Datagrid rowClick="edit">
                <TextField source="id" label="ID"/>
                <TextField source="short_name" label="Краткое наименование"/>
                <TextField source="full_name" label="Полное наименование"/>
                <LegalFormTypeReferenceField/>
            </Datagrid>
        </List>
    )
}

export default LegalFormList
