import {Theme} from '@mui/material/styles'
import {makeStyles} from '@mui/styles'
import {parse} from 'query-string'
import {Create, CreateProps, required, SaveButton, SimpleForm, Toolbar, ToolbarProps} from 'react-admin'
import {CommentInput} from '../comment'
import {MoneyInput} from '../money'
import PartReferenceInput from '../part/PartReferenceInput'
import {QuantityInput} from '../part_transfer/QuantityField'

const useStyles = makeStyles((theme: Theme) => ({
    button: {
        marginRight: theme.spacing(2),
    },
}))


const IncomePartCreateToolbar = (props: ToolbarProps) => {
    const classes = useStyles()

    return (
        <Toolbar {...props}>
            <SaveButton disabled={props.pristine} className={classes.button}/>
            <SaveButton disabled={props.pristine} redirect={false} label="Сохранить и создать ещё"
                        className={classes.button}/>
        </Toolbar>
    )
}

const IncomePartCreate = (props: CreateProps) => {
    const {income_id} = parse(props.location!!.search)

    const redirect = `/income/${income_id}/parts`

    return (
        <Create
            {...props}
        >
            <SimpleForm
                toolbar={<IncomePartCreateToolbar/>}
                redirect={redirect}
                initialValues={{income_id: income_id}}
            >
                <PartReferenceInput validate={required()}/>
                <QuantityInput validate={required()}/>
                <MoneyInput validate={required()}/>
                <CommentInput/>
            </SimpleForm>
        </Create>
    )
}

export default IncomePartCreate
