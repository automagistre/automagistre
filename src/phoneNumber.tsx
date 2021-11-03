import {TextField, TextFieldProps, TextInput, TextInputProps} from "react-admin";

interface InputProps {
    source?: string;
}

export const PhoneNumberInput = (props: InputProps & Omit<TextInputProps, 'source'>) => {
    return <TextInput
        source="telephone"
        {...props}
    />
};

PhoneNumberInput.defaultProps = {
    label: "Телефон",
    addLabel: true,
};

export const PhoneNumberField = (props: TextFieldProps) => {
    return <TextField
        source="telephone"
        {...props}
    />
};

PhoneNumberField.defaultProps = {
    label: "Телефон",
    addLabel: true,
};

// TODO Валидация телефонов
