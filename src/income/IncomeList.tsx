import {Chip} from '@mui/material'
import {Datagrid, DateField, EditButton, FieldProps, List, ListProps, NumberField, useRecordContext} from 'react-admin'
import ContactReferenceField from '../contact/ContactReferenceField'
import {MoneyField} from '../money'
import {Income} from '../types'

const IncomeList = (props: ListProps) => (
    <List {...props}
          title="Приходы"
          sort={{field: 'created_at', order: 'DESC'}}
    >
        <Datagrid>
            <ContactReferenceField source="supplier_id"/>
            <IncomeStatusField label="Статус"/>
            <MoneyField/>
            <NumberField source="items" label="Позиций"/>
            <DateField source="accrued_at" label="Оприходован" showTime={true}/>
            <EditButton/>
        </Datagrid>
    </List>
)

const IncomeStatusField = (props: FieldProps<Income>) => {
    const record = useRecordContext(props)

    if (!record.accrued_at) {
        return <Chip label="Открыт" size="small" color="warning"/>
    }

    return <Chip label="Оприходован" size="small" color="info"/>
}

export default IncomeList
