import {Box, Card, CardContent, Divider, Tab, Tabs, Typography} from '@mui/material'
import * as React from 'react'
import {ChangeEvent, useState} from 'react'
import {Datagrid, Pagination, ReferenceManyField, ShowBase, ShowProps, TextField, useShowContext} from 'react-admin'
import LegalFormReferenceField from '../legal_forms/LegalFormReferenceField'
import {PhoneNumberField} from '../phoneNumber'
import {Contact} from '../types'
import ContactNameField from './ContactNameField'
import ContactReferenceField from './ContactReferenceField'

const ContactShow = (props: ShowProps) => (
    <>
        <ShowBase {...props}>
            <ContactShowContent/>
        </ShowBase>
    </>
)

const ContactShowContent = () => {
    const {record, loaded} = useShowContext<Contact>()
    const [value, setValue] = useState(0)

    const handleChange = (_event: ChangeEvent<{}>, newValue: number) => {
        setValue(newValue)
    }

    if (!loaded || !record) return null

    return (
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
                                    <LegalFormReferenceField format="long"/>
                                </Typography>
                            </Box>
                            <PhoneNumberField link={true}/>
                        </Box>
                        <Tabs
                            value={value}
                            indicatorColor="primary"
                            textColor="primary"
                            onChange={handleChange}
                        >
                            <Tab label="1 Контакт"/>
                            <Tab label="3 Автомобиля"/>
                            <Tab label="2 Заказа"/>
                            <Tab label="10 Проводок"/>
                        </Tabs>
                        <Divider/>
                        <TabPanel value={value} index={0}>
                            <ReferenceManyField
                                reference="contact_relation"
                                target="source_id"
                                sort={{field: 'updated_at', order: 'DESC'}}
                                pagination={<Pagination/>}
                                fullWidth
                            >
                                <Datagrid>
                                    <ContactReferenceField source="target_id" label="Название"/>
                                    <TextField source="comment" label="Комментарий"/>
                                </Datagrid>
                            </ReferenceManyField>
                        </TabPanel>
                    </CardContent>
                </Card>
            </Box>
        </Box>
    )
}

interface TabPanelProps {
    children?: React.ReactNode;
    index: any;
    value: any;
}

const TabPanel = (props: TabPanelProps) => {
    const {children, value, index, ...other} = props

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`wrapped-tabpanel-${index}`}
            aria-labelledby={`wrapped-tab-${index}`}
            {...other}
        >
            {children}
        </div>
    )
}

export default ContactShow
