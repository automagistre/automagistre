import {Box, Typography} from '@mui/material'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import {
    BooleanInput,
    Datagrid,
    EditBase,
    EditProps,
    FormTab,
    Pagination,
    ReferenceManyField,
    required,
    TabbedForm,
    TextField,
    TextInput,
} from 'react-admin'
import LegalFormReferenceField from '../legal_forms/LegalFormReferenceField'
import LegalFormReferenceInput from '../legal_forms/LegalFormReferenceInput'
import {PhoneNumberField, PhoneNumberInput} from '../phoneNumber'
import ContactNameField from './ContactNameField'
import ContactNameInput from './ContactNameInput'
import ContactReferenceField from './ContactReferenceField'

const ContactEdit = (props: EditProps) => {
    return (
        <EditBase {...props} title={<ContactNameField/>} actions={false}>
            <>
                <Box mt={2} display="flex">
                    <Box flex="1">
                        <Card>
                            <CardContent>

                                <Box display="flex" mb={1}>
                                    <Box ml={2} flex="1">
                                        <Typography variant="h5">
                                            <ContactNameField/>
                                        </Typography>
                                        <Typography variant="body2">
                                            <PhoneNumberField link={true}/>
                                        </Typography>
                                    </Box>
                                    <LegalFormReferenceField format="long"/>
                                </Box>
                                <TabbedForm>
                                    <FormTab label="Информация">
                                        <LegalFormReferenceInput
                                            validate={required()}
                                        />
                                        <ContactNameInput/>
                                        <>
                                            <PhoneNumberInput/>{' '}
                                            <TextInput source="email" type="email"/>
                                        </>
                                        <BooleanInput source="contractor" label="Подрядчик"/>
                                        <BooleanInput source="supplier" label="Поставщик"/>
                                    </FormTab>
                                    <FormTab label="1 Контакт" path="relations">
                                        <ReferenceManyField
                                            reference="contact_relation"
                                            target="source_id"
                                            sort={{field: 'updated_at', order: 'DESC'}}
                                            pagination={<Pagination/>}
                                            addLabel={false}
                                            fullWidth
                                        >
                                            <Datagrid>
                                                <ContactReferenceField source="target_id" label="Название"/>
                                                <TextField source="comment" label="Комментарий"/>
                                            </Datagrid>
                                        </ReferenceManyField>
                                    </FormTab>
                                </TabbedForm>
                            </CardContent>
                        </Card>
                    </Box>
                </Box>
            </>
        </EditBase>
    )
}

export default ContactEdit
