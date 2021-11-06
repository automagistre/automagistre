import {Box, Typography} from '@mui/material'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import {
    EditBase,
    EditProps,
    FormTab,
    FormTabProps,
    NumberInput,
    SelectInput,
    TabbedForm,
    TextInput,
    useEditContext,
    useRecordContext,
} from 'react-admin'
import {CommentInput} from '../comment'
import {Vehicle} from '../types'
import VehicleAirIntakeReferenceInput from '../vehicle_air_intake/VehicleAirIntakeReferenceInput'
import VehicleBodyReferenceInput from '../vehicle_bodies/VehicleBodyReferenceInput'
import VehicleBodyTypeReferenceInput from '../vehicle_body_types/VehicleBodyTypeReferenceInput'
import VehicleDriveWheelReferenceInput from '../vehicle_drive_wheel/VehicleDriveWheelReferenceInput'
import VehicleFuelTypeReferenceInput from '../vehicle_fuel_type/VehicleFuelTypeReferenceInput'
import VehicleInjectionReferenceInput from '../vehicle_injection/VehicleInjectionReferenceInput'
import VehicleTransmissionReferenceInput from '../vehicle_transmission/VehicleTransmissionReferenceInput'

const VehicleEdit = (props: EditProps) => {
    return (
        <EditBase {...props} actions={false}>
            <VehicleEditContent/>
        </EditBase>
    )
}

const VehicleEditContent = () => {
    const {record, loaded, save} = useEditContext<Vehicle>()

    if (!loaded || !record) return null

    return (
        <Box mt={2} display="flex">
            <Box flex="1">
                <Card>
                    <CardContent>
                        <Box display="flex" mb={1}>
                            <Box ml={2} flex="1">
                                <Typography variant="h5">
                                    TODO Name
                                </Typography>
                                <Typography variant="body2">
                                    TODO Description
                                </Typography>
                            </Box>
                            TODO ?
                        </Box>
                        <TabbedForm record={record} save={save}>
                            <MainTab label="Редактировать"/>
                            <FormTab label="1 Контакт" path="contact">

                            </FormTab>
                        </TabbedForm>
                    </CardContent>
                </Card>
            </Box>
        </Box>
    )
}

const Range = (start: number, stop: number, step: number) => Array.from({length: (stop - start) / step + 1}, (_, i) => start + (i * step))

const MainTab = (props: FormTabProps) => {
    const record = useRecordContext<Vehicle>()

    return (
        <FormTab record={record} {...props}>
            <VehicleBodyReferenceInput/>
            <NumberInput source="year"/>
            <VehicleBodyTypeReferenceInput/>
            <TextInput source="identifier" label="Идентификатор" helperText="VIN, Кузов, Шасси"/>
            <TextInput source="legal_plate" label="Гос. номер"/>
            <CommentInput/>
            <Typography>Комплектация</Typography>
            <TextInput source="engine_name"/>
            <SelectInput
                choices={(() => Range(0.6, 6.0, 0.1).map(function (i) {
                    const number = parseFloat(String(i)).toFixed(1)

                    return {id: number, name: number}
                }))()}
                allowNull={true}
                source="engine_capacity"
            />
            <VehicleAirIntakeReferenceInput/>
            <VehicleDriveWheelReferenceInput/>
            <VehicleFuelTypeReferenceInput/>
            <VehicleInjectionReferenceInput/>
            <VehicleTransmissionReferenceInput/>
        </FormTab>
    )
}

export default VehicleEdit
