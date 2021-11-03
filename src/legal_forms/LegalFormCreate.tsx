import {Create, CreateProps, required, SimpleForm, TextInput,} from 'react-admin';
import LegalFormTypeReferenceInput from "./LegalFormTypeReferenceInput";

const LegalFormCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm redirect="list">
                <TextInput source="id" label="ID" validate={required()}/>
                <TextInput source="short_name" label="Краткое наименование" validate={required()}/>
                <TextInput source="full_name" label="Полное наименование" validate={required()} fullWidth/>
                <LegalFormTypeReferenceInput validate={required()}/>
            </SimpleForm>
        </Create>
    );
};

export default LegalFormCreate;
