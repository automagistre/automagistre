import {Chip} from '@mui/material'
import {makeStyles} from '@mui/styles'
import {OrderStatus} from '../types'

const useStyles = makeStyles({
    chip: {},
})

const OrderStatusField = ({record}: { record?: OrderStatus }) => {
    const classes = useStyles()

    if (!record) return null

    return <Chip label={record.name} size="small" className={classes.chip} color={record.color}/>
}

export default OrderStatusField
