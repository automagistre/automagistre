import {
    BooleanField,
    CreateButton,
    Datagrid,
    ReferenceManyField,
    Show,
    SimpleShowLayout,
    SimpleShowLayoutProps,
    TextField,
    useRecordContext,
} from 'react-admin';
import LegalFormReferenceField from "../legal_forms/LegalFormReferenceField";
import {PhoneNumberField} from "../phoneNumber";
import ContactNameField from "./ContactNameField";
import {Contact} from "../types";
import Typography from "@material-ui/core/Typography";
import CardContent from "@material-ui/core/CardContent";
import Card from "@material-ui/core/Card";
import {CardHeader} from "@material-ui/core";
import ContactReferenceField from "./ContactReferenceField";

interface ContactTitleProps {
    record?: Contact;
}

const ContactTitle = ({record}: ContactTitleProps) => record ? <ContactNameField record={record}/> : null;

const Aside = () => {
    console.log(useRecordContext())

    return (
        <Card>
            <CardContent>
                <Typography variant="h6">Post details</Typography>
                <Typography variant="body2">
                    Posts will only be published once an editor approves them
                </Typography>
            </CardContent>
        </Card>
    );
};

const ContactShow = (props: SimpleShowLayoutProps) => {

    console.log(useRecordContext())

    return (
        <Show
            title={<ContactTitle/>}
            aside={<Aside/>}
            {...props}
        >
            <>
                <SimpleShowLayout>
                    <LegalFormReferenceField/>
                    <ContactNameField/>
                    <PhoneNumberField/>
                    <BooleanField source="contractor" label="Подрядчик"/>
                    <BooleanField source="supplier" label="Поставщик"/>
                </SimpleShowLayout>

                <Card>
                    <CardHeader title="Связанные контакты"/>
                    <CardContent>
                        <CreateButton basePath="/contact_reference" label="Добавить связь"/>

                        <ReferenceManyField reference="contact_reference" target="source_id" addLabel={false}>
                            <Datagrid>
                                <ContactReferenceField source="target_id"/>
                                <TextField source="comment" label="Комментарий"/>
                            </Datagrid>
                        </ReferenceManyField>
                    </CardContent>
                </Card>
            </>
        </Show>
    );
};

export default ContactShow;
