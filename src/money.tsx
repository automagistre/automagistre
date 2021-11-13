import {NumberField, NumberFieldProps, NumberInput, NumberInputProps} from 'react-admin'

export const MoneyInput = (props: NumberInputProps) => {
    return <NumberInput
        {...props}
    />
}

MoneyInput.defaultProps = {
    source: 'amount',
    label: 'Сумма',
}

export const MoneyField = (props: NumberFieldProps) => {
    return <NumberField
        {...props}
        options={{style: 'currency', currency: 'RUB'}}
    />
}

MoneyField.defaultProps = {
    source: 'amount',
    label: 'Сумма',
}
