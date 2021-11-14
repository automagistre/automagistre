import {NumberField, NumberFieldProps, NumberInput, NumberInputProps} from 'react-admin'

export const QuantityField = (props: NumberFieldProps) => {
    return <NumberField {...props} />
}

export const QuantityInput = (inputProps: Omit<NumberInputProps, 'source'>) => {
    const props = inputProps as NumberInputProps

    return <NumberInput {...props} />
}

const defaultProps = {
    source: 'quantity',
    label: 'Количество',
}
QuantityField.defaultProps = {
    options: {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    },
    ...defaultProps,
}
QuantityInput.defaultProps = defaultProps
