import * as React from 'react';
import {ChangeEvent, useState} from 'react';
import {
    Identifier,
    ReferenceManyField,
    ShowBase,
    ShowProps,
    useListContext,
    useRecordContext,
    useRedirect,
    useShowContext,
} from 'react-admin';
import {
    Box,
    Button,
    Card,
    CardContent,
    Divider,
    List,
    ListItem,
    ListItemSecondaryAction,
    ListItemText,
    Tab,
    Tabs,
    Typography,
} from '@material-ui/core';
import PersonAddIcon from '@material-ui/icons/PersonAdd';
import {Route} from 'react-router-dom';
import {formatDistance} from 'date-fns';
import {ru} from 'date-fns/locale'

import {ContactAside} from './ContactAside';
import {Contact, ContactRelation} from '../types';
import ContactNameField from "./ContactNameField";
import ContactRelationCreate from "./ContactRelationCreate";
import ContactReferenceField from "./ContactReferenceField";
import LegalFormReferenceField from "../legal_forms/LegalFormReferenceField";
import EditButton from '@material-ui/icons/Edit';
import DeleteIcon from '@material-ui/icons/Delete';
import IconButton from '@material-ui/core/IconButton';

const ContactShow = (props: ShowProps) => (
    <>
        <ShowBase {...props}>
            <ContactShowContent/>
        </ShowBase>
        <Route path="/contacts/:id/relation/create">
            {({match}) => <ContactRelationCreate open={!!match} contactId={match?.params?.id!!}/>}
        </Route>
        {/*<Route path="/deals/:id/show">*/}
        {/*    {({ match }) =>*/}
        {/*        !!match ? (*/}
        {/*            <DealShow open={!!match} id={match?.params?.id} />*/}
        {/*        ) : null*/}
        {/*    }*/}
        {/*</Route>*/}
    </>
);

const ContactShowContent = () => {
    const {record, loaded} = useShowContext<Contact>();
    const [value, setValue] = useState(0);
    const handleChange = (_event: ChangeEvent<{}>, newValue: number) => {
        setValue(newValue);
    };
    if (!loaded || !record) return null;

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
                                sort={{field: 'updated_at', order: 'ASC'}}
                            >
                                <ContactsIterator/>
                            </ReferenceManyField>
                        </TabPanel>
                    </CardContent>
                </Card>
            </Box>
            <ContactAside record={record}/>
        </Box>
    );
};

interface TabPanelProps {
    children?: React.ReactNode;
    index: any;
    value: any;
}

const TabPanel = (props: TabPanelProps) => {
    const {children, value, index, ...other} = props;

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
    );
};

const ContactsIterator = () => {
    const {data, ids, loaded} = useListContext<ContactRelation>();
    const record = useRecordContext<ContactRelation>();
    const redirect = useRedirect();

    if (!loaded) return null;

    return (
        <Box>
            <List>
                {ids.map(id => {
                    const relation: ContactRelation = data[id];

                    return (
                        <ListItem
                            button
                            key={id}
                            onClick={() => redirect(`/contact/${relation.target_id}/show`)}
                        >
                            <ListItemText
                                primary={<ContactReferenceField source="target_id" record={relation}/>}
                                secondary={relation.comment}
                            />
                            <Typography
                                variant="body2"
                                color="textSecondary"
                                component="span"
                            >
                                Обновлён {formatDistance(new Date(relation.updated_at), Date.now(), {locale: ru})} назад
                            </Typography>

                            <ListItemSecondaryAction>
                                <IconButton edge="end" aria-label="delete" onClick={() => console.log('lol')}>
                                    <EditButton/>
                                </IconButton>
                                <IconButton edge="end" aria-label="delete" onClick={() => console.log('lol')}>
                                    <DeleteIcon/>
                                </IconButton>
                            </ListItemSecondaryAction>
                        </ListItem>
                    );
                })}
            </List>
            <Box textAlign="center" mt={1}>
                <CreateRelatedContactButton id={record.id}/>
            </Box>
        </Box>
    );
};

const CreateRelatedContactButton = ({id}: { id: Identifier }) => {
    const redirect = useRedirect();

    return (
        <Button
            onClick={() => redirect(`/contacts/${id}/relation/create`)}
            color="primary"
            variant="contained"
            size="small"
            startIcon={<PersonAddIcon/>}
        >
            Добавить связь
        </Button>
    );
};

export default ContactShow;
