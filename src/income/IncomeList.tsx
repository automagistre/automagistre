import {
    Datagrid,
    DateField,
    DeleteButton,
    DeleteButtonProps,
    List,
    ListProps,
    NumberField,
    useRecordContext,
} from 'react-admin'
import ContactReferenceField from '../contact/ContactReferenceField'
import {MoneyField} from '../money'
import {Income} from '../types'
import IncomeStatusReferenceField from './IncomeStatusReferenceField'

const IncomeList = (props: ListProps) => (
    <List {...props}
          title="Приходы"
          sort={{field: 'created_at', order: 'DESC'}}
    >
        <Datagrid rowClick="edit">
            <ContactReferenceField source="supplier_id"/>
            <IncomeStatusReferenceField/>
            <MoneyField/>
            <NumberField source="items" label="Позиций"/>
            <DateField source="accrued_at" label="Оприходован" showTime={true}/>
            <DeleteButtonIfNotAccrued/>
        </Datagrid>
    </List>
)

const DeleteButtonIfNotAccrued = (props: DeleteButtonProps) => {
    const income = useRecordContext<Income>()

    if (income.accrued_at) {
        return null
    }
    return <DeleteButton {...props}/>
}

export default IncomeList
