import {ReferenceField, ReferenceFieldProps} from 'react-admin'
import IncomeRefField from './IncomeRefField'

const IncomeReferenceField = (props: Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>) => {
    const rest = props as ReferenceFieldProps

    return (
        <ReferenceField
            {...rest}
        >
            <IncomeRefField/>
        </ReferenceField>
    )
}

IncomeReferenceField.defaultProps = {
    source: 'income_id',
    reference: 'income',
    label: 'Приход',
}

export default IncomeReferenceField
