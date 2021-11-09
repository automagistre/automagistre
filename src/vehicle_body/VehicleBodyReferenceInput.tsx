import {AutocompleteInput, ReferenceInput, ReferenceInputProps} from 'react-admin'
import {VehicleBody} from '../types'
import VehicleBodyNameField from './VehicleBodyNameField'


const VehicleBodyReferenceInput = (props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>) => (
    <ReferenceInput
        {...props}
        source="vehicle_body_id"
        reference="vehicle_body"
        enableGetChoices={({q}: { q?: string }) => q && q.length >= 2}
        filterToQuery={searchText => ({'manufacturer#name@_ilike,manufacturer#localized_name@_ilike,name@_ilike,localized_name@_ilike,case_name@_ilike': searchText})}
    >
        <AutocompleteInput
            optionText={<VehicleBodyNameField/>}
            inputText={(record: VehicleBody) => `${record.name ?? record.localized_name} ${record.case_name ?? ''}`}
            matchSuggestion={() => true}
        />
    </ReferenceInput>
)

VehicleBodyReferenceInput.defaultProps = {
    source: 'vehicle_body_id',
    reference: 'vehicle_body',
    label: 'Кузов',
}

export default VehicleBodyReferenceInput
