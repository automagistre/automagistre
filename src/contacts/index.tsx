import ContactList from './ContactList';
import ContactEdit from './ContactEdit';
import ContactCreate from './ContactCreate';
import {faUsers} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import ContactShow from "./ContactShow";

export default {
    list: ContactList,
    create: ContactCreate,
    edit: ContactEdit,
    show: ContactShow,
    icon: <FontAwesomeIcon icon={faUsers}/>,
};
