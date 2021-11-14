import {ReferenceField, ReferenceFieldProps} from 'react-admin'
import PartNameField from './PartNameField'

const PartReferenceField = (props: Omit<Omit<ReferenceFieldProps, 'source'>, 'reference' | 'children'>) => {
    const rest = props as ReferenceFieldProps

    return (
        <ReferenceField
            {...rest}
        >
            <PartNameField/>
        </ReferenceField>
    )
}

PartReferenceField.defaultProps = {
    label: 'Запчасть',
    reference: 'part',
    source: 'part_id',
    link: false,
}

export default PartReferenceField
