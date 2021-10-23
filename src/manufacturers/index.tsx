import ManufacturerList from './ManufacturerList';
import ManufacturerEdit from './ManufacturerEdit';
import ManufacturerCreate from './ManufacturerCreate';
import {faIndustry} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

export default {
    list: ManufacturerList,
    create: ManufacturerCreate,
    edit: ManufacturerEdit,
    icon: <FontAwesomeIcon icon={faIndustry}/>,
};
