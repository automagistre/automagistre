import {
    AutocompleteInput,
    Create,
    CreateProps,
    NumberInput,
    ReferenceInput,
    required,
    SimpleForm,
    TextInput,
} from 'react-admin'

const VehicleBodyCreate = (props: CreateProps) => {
    return (
        <Create {...props}>
            <SimpleForm redirect="list">
                <ReferenceInput
                    source="manufacturer_id"
                    reference="manufacturer"
                    label="Производитель"
                    validate={required()}
                    filterToQuery={searchText => ({'name,localized_name': searchText})}
                >
                    <AutocompleteInput optionText="name" source="name"/>
                </ReferenceInput>
                <TextInput
                    source="name"
                    validate={required()}
                    label="Название"
                />
                <TextInput
                    source="localized_name"
                    label="Название на русском"
                />
                <TextInput
                    source="case_name"
                    label="Кузов"
                />
                <NumberInput
                    source="year_from"
                    label="Начало производства"
                />
                <NumberInput
                    source="year_till"
                    label="Конец производства"
                />
            </SimpleForm>
        </Create>
    )
}

export default VehicleBodyCreate
