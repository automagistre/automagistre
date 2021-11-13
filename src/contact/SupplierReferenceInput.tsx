import {ReferenceInput, ReferenceInputProps, SelectInput} from 'react-admin'
import ContactNameField from './ContactNameField'


const SupplierReferenceInput = (props: Omit<Omit<ReferenceInputProps, 'source'>, 'reference' | 'children'>) => {
    return (
        <ReferenceInput
            {...props}
            reference="contact"
            filter={{supplier: true}}
            filterToQuery={searchText => ({'telephone': searchText})}
            source="supplier_id"
        >
            <SelectInput optionText={<ContactNameField/>}/>
        </ReferenceInput>
    )
}

SupplierReferenceInput.defaultProps = {
    label: 'Поставщик',
    source: 'supplier_id',
}

export default SupplierReferenceInput
