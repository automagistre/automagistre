import {Fragment} from 'react'
import {FieldProps, useRecordContext, useReference} from 'react-admin'
import {Part} from '../types'


const PartNameField = (props: FieldProps<Part>) => {
    const record = useRecordContext(props)
    const {loading, loaded, referenceRecord} = useReference({
        id: record.manufacturer_id.toString(),
        reference: 'manufacturer',
    })

    if (!loaded || loading || !referenceRecord) return null

    return (
        <Fragment>
            {referenceRecord.name} - ({record.name} {record.number})
        </Fragment>
    )
}

PartNameField.defaultProps = {
    label: 'Запчасть',
    source: 'part_id',
    addLabel: true,
}

export default PartNameField
