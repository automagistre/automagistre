import {
    BooleanInput,
    Create,
    CreateProps,
    ReferenceInput,
    required,
    SelectInput,
    SimpleForm,
    TextInput,
} from 'react-admin'
import ManufacturerReferenceInput from '../manufacturer/ManufacturerReferenceInput'

const PartCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm redirect="list">
                <ManufacturerReferenceInput validate={required()}/>
                <TextInput
                    source="name"
                    validate={required()}
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
                    initialValue={false}
                />
                <ReferenceInput
                    source="unit"
                    reference="unit"
                    label="Единица измерения"
                    validate={required()}
                >
                    <SelectInput optionText="name"/>
                </ReferenceInput>
                <TextInput
                    source="comment"
                    label="Комментарий"
                />
            </SimpleForm>
        </Create>
    )
}

export default PartCreate
