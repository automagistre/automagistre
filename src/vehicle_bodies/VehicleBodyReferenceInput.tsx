import {AutocompleteInput, ReferenceInput, ReferenceInputProps} from 'react-admin'
import {VehicleBody} from '../types'

interface Props {
    source?: string;
}

const VehicleBodyReferenceInput = (
    props: Props &
        Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>,
) => (
    <ReferenceInput
        {...props}
        source="body"
        reference="vehicle_body"
        enableGetChoices={({q}: { q?: string }) => q && q.length >= 2}
        filterToQuery={searchText => ({'manufacturer#name@_like,manufacturer#localized_name@_like,name@_like,localized_name@_like,case_name@_like': searchText})}
    >
        <AutocompleteInput
            optionText={(choice: VehicleBody) =>
                choice.id // the empty choice is { id: '' }
                    ? `${choice.name} ${choice.localized_name} ${choice.case_name}`
                    : ''}
        />
    </ReferenceInput>
)

VehicleBodyReferenceInput.defaultProps = {
    source: 'body',
    reference: 'vehicle_body',
    label: 'Кузов',
    addLabel: true,
}

export default VehicleBodyReferenceInput
