import {Dialog} from '@mui/material'
import {makeStyles} from '@mui/styles'
import {Create, Identifier, required, SimpleForm, TextInput, useRedirect} from 'react-admin'

const useStyles = makeStyles({
    root: {
        width: 500,
    },
})

const ContactRelationCreate = ({open, contactId}: { open: boolean, contactId: Identifier }) => {
    const classes = useStyles()
    const redirect = useRedirect()

    const handleClose = () => {
        redirect('/contact/' + contactId)
    }

    const onSuccess = () => {
        console.log('onSuccess')
    }

    return (
        <Dialog open={open} onClose={handleClose}>
            <Create
                resource="deals"
                basePath="/deals"
                className={classes.root}
                onSuccess={onSuccess}
            >
                <SimpleForm initialValues={{index: 0}}>
                    <TextInput
                        source="name"
                        label="Deal name"
                        fullWidth
                        validate={[required()]}
                    />
                </SimpleForm>
            </Create>
        </Dialog>
    )
}

export default ContactRelationCreate
