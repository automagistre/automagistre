import {Datagrid, DateField, FieldProps, List, ListProps, useRecordContext, useReference} from 'react-admin'
import OrderReferenceField from '../order/OrderReferenceField'
import PartReferenceField from '../part/PartReferenceField'
import {PartTransfer} from '../types'
import IncomeReferenceField from './IncomeReferenceField'
import {QuantityField} from './QuantityField'

const PartTransferList = (props: ListProps) => (
    <List {...props}
          title="Движения склада"
          sort={{field: 'created_at', order: 'DESC'}}
    >
        <Datagrid>
            <PartReferenceField link={true}/>
            <QuantityField/>
            <DescriptionField label="Описание"/>
            <DateField source="created_at" label="Создан" showTime={true}/>
        </Datagrid>
    </List>
)

const DescriptionField = (props: FieldProps<PartTransfer>) => {
    const record = useRecordContext(props)

    if (record.reason === 'order') {
        return <OrderReferenceField source="reason_id"/>
    }

    if (record.reason === 'income') {
        return <DescriptionIncome {...props}/>
    }

    if (record.reason === 'manual') {
        return <span>Ручная проводка</span> // TODO
    }

    console.error(`Unexpected part_transfer reason: ${record.reason}`)

    return null
}

const DescriptionIncome = (props: FieldProps<PartTransfer>) => {
    const record = useRecordContext(props)

    const {loaded, referenceRecord} = useReference({
        id: record.reason_id,
        reference: 'income_part',
    })

    if (!loaded) return <span>Приход</span>

    return <IncomeReferenceField record={referenceRecord}/>
}

export default PartTransferList
