import {Chip} from '@mui/material'
import {IncomeStatus} from '../types'

const IncomeStatusField = ({record}: { record?: IncomeStatus }) => {
    if (!record) return null

    return <Chip label={record.name} size="small" color={record.color}/>
}

export default IncomeStatusField
