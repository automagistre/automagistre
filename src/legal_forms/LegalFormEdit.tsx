import {Edit, EditProps, required, SimpleForm, TextInput,} from 'react-admin';
import {Contact} from '../types';
import LegalFormTypeReferenceInput from "./LegalFormTypeReferenceInput";

interface LegalFormTitleProps {
    record?: Contact;
}

const LegalFormTitle = ({record}: LegalFormTitleProps) => record ?
    <span>Правовая форма {record.short_name}</span> : null;

const LegalFormEdit = (props: EditProps) => {
    return (
        <Edit {...props} title={<LegalFormTitle/>}>
            <SimpleForm>
                <TextInput source="id" label="ID" validate={required()}/>
                <TextInput source="short_name" label="Краткое наименование" validate={required()}/>
                <TextInput source="full_name" label="Полное наименование" validate={required()} fullWidth/>
                <LegalFormTypeReferenceInput validate={required()}/>
            </SimpleForm>
        </Edit>
    );
};

export default LegalFormEdit;
