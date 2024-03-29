import {Link} from '@mui/material'
import parsePhoneNumber from 'libphonenumber-js'
import {TextInput, TextInputProps, useRecordContext} from 'react-admin'

interface InputProps {
    source?: string;
}

export const PhoneNumberInput = (props: InputProps & Omit<TextInputProps, 'source'>) => {
    return <TextInput
        source="telephone"
        {...props}
    />
}

PhoneNumberInput.defaultProps = {
    label: 'Телефон',
}

interface PhoneNumberFieldProps {
    source?: string;
    link?: boolean,
}

export const PhoneNumberField = (props: PhoneNumberFieldProps) => {
    const record = useRecordContext()

    if (!record) return null

    const telephone: string = record[props.source ?? 'telephone']

    if (!telephone) return null

    const phoneNumber = parsePhoneNumber(telephone)

    const value = phoneNumber?.formatInternational()

    if (props.link) {
        return <Link href={phoneNumber?.getURI()}>{value}</Link>
    }

    return <span>{value}</span>
}

PhoneNumberField.defaultProps = {
    label: 'Телефон',
}

// TODO Валидация телефонов
