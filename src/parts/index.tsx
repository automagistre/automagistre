import PartList from './PartList';
import PartEdit from './PartEdit';
import PartCreate from './PartCreate';
import {faCogs} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

export default {
    list: PartList,
    create: PartCreate,
    edit: PartEdit,
    icon: <FontAwesomeIcon icon={faCogs}/>,
};
