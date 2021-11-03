import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin';
import {LegalForm} from "../types";

interface Props {
    source?: string;
}

const LegalFormReferenceInput = (
    props: Props &
        Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceInput
        {...props}
        source="legal_form"
        reference="legal_form"
    >
        <SelectInput optionText={(value: LegalForm) => `(${value.short_name}) -  ${value.full_name}`}/>
    </ReferenceInput>
);

LegalFormReferenceInput.defaultProps = {
    label: "Правовая форма",
};

export default LegalFormReferenceInput;
