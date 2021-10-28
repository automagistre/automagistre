import WalletList from './WalletList';
import WalletEdit from './WalletEdit';
import WalletCreate from './WalletCreate';
import {faWallet} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

export default {
    list: WalletList,
    create: WalletCreate,
    edit: WalletEdit,
    icon: <FontAwesomeIcon icon={faWallet}/>,
};
