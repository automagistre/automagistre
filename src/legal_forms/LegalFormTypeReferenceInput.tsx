import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin';

interface Props {
    source?: string;
}

const LegalFormTypeReferenceInput = (
    props: Props &
        Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceInput
        source="type"
        reference="legal_form_type"
        {...props}
    >
        <SelectInput source="name" helperText={props.helperText}/>
    </ReferenceInput>
);

LegalFormTypeReferenceInput.defaultProps = {
    label: "Тип правовой формы",
    source: 'type',
    helperText: 'ФИО или Название/Полное название',
};

export default LegalFormTypeReferenceInput;
