import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin';

interface Props {
    source?: string;
}

const ContactTypeReferenceInput = (
    props: Props &
        Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>
) => (
    <ReferenceInput
        {...props}
        source="type"
        reference="contact_type"
        defaultValue="UNKNOWN"
    >
        <SelectInput optionText={(value) => value.name.full}/>
    </ReferenceInput>
);

ContactTypeReferenceInput.defaultProps = {
    label: "Правовая форма",
    source: 'type',
    addLabel: true,
    value: 'UNKNOWN',
};

export default ContactTypeReferenceInput;
