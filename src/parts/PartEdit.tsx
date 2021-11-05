import {
    BooleanInput,
    Edit,
    EditProps,
    ReferenceInput,
    required,
    SelectInput,
    SimpleForm,
    TextInput,
} from 'react-admin';
import {Part} from '../types';
import ManufacturerReferenceInput from "../manufacturers/ManufacturerReferenceInput";

interface PartTitleProps {
    record?: Part;
}

const PartTitle = ({record}: PartTitleProps) => record ?
    <span>Запчасть {record.name} #{record.number}</span> : null;

const PartEdit = (props: EditProps) => {
    return (
        <Edit {...props} title={<PartTitle/>}>
            <SimpleForm>
                <ManufacturerReferenceInput validate={required()}/>
                <TextInput
                    source="name"
                    label="Название"
                />
                <TextInput
                    source="number"
                    label="Номер"
                    validate={required()}
                />
                <BooleanInput
                    source="universal"
                    label="Универсальная"
                />
                <ReferenceInput
                    source="unit"
                    reference="unit"
                    label="Единица измерения"
                >
                    <SelectInput optionText="name"/>
                </ReferenceInput>
                <TextInput
                    source="comment"
                    label="Комментарий"
                />
            </SimpleForm>
        </Edit>
    );
};

export default PartEdit;
