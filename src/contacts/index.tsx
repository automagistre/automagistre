import ContactList from './ContactList';
import ContactEdit from './ContactEdit';
import ContactCreate from './ContactCreate';
import {faUsers} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

export default {
    list: ContactList,
    create: ContactCreate,
    edit: ContactEdit,
    icon: <FontAwesomeIcon icon={faUsers}/>,
};
