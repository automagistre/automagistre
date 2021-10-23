import VehicleList from './VehicleList';
import VehicleEdit from './VehicleEdit';
import VehicleCreate from './VehicleCreate';
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faCar} from "@fortawesome/free-solid-svg-icons";

export default {
    list: VehicleList,
    create: VehicleCreate,
    edit: VehicleEdit,
    icon: <FontAwesomeIcon icon={faCar}/>,
};
