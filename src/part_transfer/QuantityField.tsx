import {NumberField, NumberFieldProps} from 'react-admin'

export const QuantityField = (props: NumberFieldProps) => {
    return <NumberField {...props} />
}

QuantityField.defaultProps = {
    source: 'quantity',
    label: 'Количество',
    options: {
        minimumFractionDigits: 2,
    },
}
