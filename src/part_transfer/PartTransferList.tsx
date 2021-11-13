import {Datagrid, DateField, FieldProps, List, ListProps, useRecordContext} from 'react-admin'
import OrderReferenceField from '../order/OrderReferenceField'
import PartReferenceField from '../part/PartReferenceField'
import {PartTransfer} from '../types'
import {QuantityField} from './QuantityField'

const PartTransferList = (props: ListProps) => (
    <List {...props}
          title="Движения склада"
          sort={{field: 'created_at', order: 'DESC'}}
    >
        <Datagrid>
            <PartReferenceField/>
            <QuantityField/>
            <DescriptionField label="Описание"/>
            <DateField source="created_at" label="Создан" showTime={true}/>
        </Datagrid>
    </List>
)

const DescriptionField = (props: FieldProps<PartTransfer>) => {
    const record = useRecordContext(props)
    console.log(record.reason)

    if (record.reason === 'order') {
        return <OrderReferenceField source="reason_id"/>
    }

    if (record.reason === 'income') {
        return <span>Приход</span> // TODO
    }

    if (record.reason === 'manual') {
        return <span>Ручная проводка</span> // TODO
    }

    console.error(`Unexpected part_transfer reason: ${record.reason}`)

    return null
}

export default PartTransferList
