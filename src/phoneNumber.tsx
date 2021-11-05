import {TextInput, TextInputProps, useRecordContext} from "react-admin";
import {Link} from '@mui/material';
import parsePhoneNumber from 'libphonenumber-js'

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

interface PhoneNumberFieldProps {
    source?: string;
    link?: boolean,
}

export const PhoneNumberField = (props: PhoneNumberFieldProps) => {
    const record = useRecordContext();

    const telephone: string = record[props.source ?? 'telephone']

    if (!telephone) return null;

    const phoneNumber = parsePhoneNumber(telephone)

    const value = phoneNumber?.formatInternational();

    if (props.link) {
        return <Link href={phoneNumber?.getURI()}>{value}</Link>
    }

    return <span>{value}</span>;
};

PhoneNumberField.defaultProps = {
    label: "Телефон",
    addLabel: true,
};

// TODO Валидация телефонов
