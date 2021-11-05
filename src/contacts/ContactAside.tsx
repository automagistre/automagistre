import {Box, Chip, Divider, Typography} from '@mui/material';
import {EditButton, ShowButton} from "react-admin";
import {Contact} from "../types";
import ContactNameField from "./ContactNameField";
import LegalFormReferenceField from "../legal_forms/LegalFormReferenceField";
import {PhoneNumberField} from "../phoneNumber";

export const ContactAside = ({record, link = 'edit'}: { record?: Contact; link?: string; }) =>
    record ? (
        <Box ml={4} width={250} minWidth={250}>
            <Box textAlign="center" mb={2}>
                {link === 'edit' ? (
                    <EditButton
                        basePath="/contact"
                        record={record}
                    />
                ) : (
                    <ShowButton
                        basePath="/contact"
                        record={record}
                    />
                )}
            </Box>

            <Typography variant="subtitle2">
                <LegalFormReferenceField format="long"/>
            </Typography>
            <Divider/>

            <Box mt={2} mb={3}>
                <ContactNameField/>
            </Box>

            <Typography variant="subtitle2">Телефон</Typography>
            <Divider/>

            <Box mt={1} mb={3}>
                <PhoneNumberField link={true}/>
            </Box>

            <Typography variant="subtitle2">Причастность</Typography>
            <Divider/>

            <Box mt={1}>
                {record.contractor && [<Chip label="Подрядчик" variant="outlined"/>, <br/>]}
                {record.supplier && [<Chip label="Поставщик" variant="outlined"/>, <br/>]}
            </Box>
        </Box>
    ) : null;
