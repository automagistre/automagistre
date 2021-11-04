import LegalFormList from './LegalFormList';
import LegalFormEdit from './LegalFormEdit';
import LegalFormCreate from './LegalFormCreate';
import {faGavel} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

const legalForms = {
    list: LegalFormList,
    create: LegalFormCreate,
    edit: LegalFormEdit,
    icon: <FontAwesomeIcon icon={faGavel}/>,
};

export default legalForms;
