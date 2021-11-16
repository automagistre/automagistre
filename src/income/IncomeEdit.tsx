import {ApolloError, gql, useApolloClient} from '@apollo/client'
import {Box, Button, Typography} from '@mui/material'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import {Theme} from '@mui/material/styles'
import {makeStyles} from '@mui/styles'
import {
    Datagrid,
    EditBase,
    EditProps,
    FormTab,
    ReferenceManyField,
    required,
    TabbedForm,
    TextField,
    TextInput,
    useEditContext,
    useNotify,
    useRecordContext,
    useRefresh,
} from 'react-admin'
import {Link} from 'react-router-dom'
import {CommentInput} from '../comment'
import ContactReferenceField from '../contact/ContactReferenceField'
import SupplierReferenceInput from '../contact/SupplierReferenceInput'
import {MoneyField} from '../money'
import PartReferenceField from '../part/PartReferenceField'
import {QuantityField} from '../part_transfer/QuantityField'
import {Income} from '../types'

const IncomeEdit = (props: EditProps) => (
    <EditBase
        {...props}
    >
        <IncomeEditContent/>
    </EditBase>
)

const IncomeEditContent = () => {
    const {record, loaded, save} = useEditContext<Income>()
    const classes = useStyles()

    if (!loaded || !record) return null

    return (
        <Box mt={2} display="flex">
            <Box flex="1">
                <Card>
                    <CardContent>
                        <Box display="flex" mb={1}>
                            <Box ml={2} flex="1">
                                <Typography variant="h5">
                                    <ContactReferenceField source="supplier_id"/>
                                </Typography>
                                <Typography variant="body2">
                                    <TextField source="document"/>
                                </Typography>
                            </Box>
                            {!record.accrued_at && <AccrueButton/>}
                        </Box>
                        <TabbedForm record={record} save={save}>
                            <FormTab label="Редактировать">
                                <SupplierReferenceInput
                                    validate={required()}
                                />
                                <TextInput
                                    source="document"
                                    label="Документ"
                                />
                                <CommentInput/>
                            </FormTab>
                            <FormTab label="Запчасти" path="parts">
                                <ReferenceManyField
                                    reference="income_part"
                                    target="income_id"
                                    sort={{field: 'created_at', order: 'ASC'}}
                                    addLabel={false}
                                    fullWidth
                                >
                                    <Datagrid rowClick={() => record?.accrued_at ? '' : 'edit'}>
                                        <PartReferenceField/>
                                        <QuantityField/>
                                        <MoneyField/>
                                        <TextField source="comment" label="Комментарий"/>
                                    </Datagrid>
                                </ReferenceManyField>

                                {!record.accrued_at && <Button
                                    component={Link}
                                    to={{
                                        pathname: `/income_part/create?income_id=${record.id}`,
                                    }}
                                    className={classes.button}
                                    variant="contained"
                                >
                                    Добавить запчасть
                                </Button>}
                            </FormTab>
                        </TabbedForm>
                    </CardContent>
                </Card>
            </Box>
        </Box>
    )
}

const AccrueButton = () => {
    const record = useRecordContext<Income>()
    const refresh = useRefresh()
    const apolloClient = useApolloClient()
    const notify = useNotify()

    const handleAccrue = () => {
        const INCOME_ACCRUE = gql`
            mutation Income($incomeId: String!) {
              income_accrue(args: {income_id: $incomeId}) {
                id
              }
            }
            `

        apolloClient
            .mutate({mutation: INCOME_ACCRUE, variables: {incomeId: record.id}})
            .then(() => {
                notify('Оприходовано', 'success')

                refresh()
            })
            .catch((e: ApolloError) => e.graphQLErrors.map(err => notify(err.extensions.internal.error.message, 'error')))
    }

    return (
        <Button variant="contained" color="success" onClick={handleAccrue}>Оприходовать</Button>
    )
}

const useStyles = makeStyles((theme: Theme) => ({
    button: {
        marginTop: theme.spacing(3),
    },
}))

export default IncomeEdit
