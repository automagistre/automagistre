import {faGavel} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import LegalFormCreate from './LegalFormCreate'
import LegalFormEdit from './LegalFormEdit'
import LegalFormList from './LegalFormList'

const legalForms = {
    list: LegalFormList,
    create: LegalFormCreate,
    edit: LegalFormEdit,
    icon: <FontAwesomeIcon icon={faGavel}/>,
}

export default legalForms
