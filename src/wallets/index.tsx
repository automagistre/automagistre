import WalletList from './WalletList';
import WalletEdit from './WalletEdit';
import WalletCreate from './WalletCreate';
import {faWallet} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

const wallets = {
    list: WalletList,
    create: WalletCreate,
    edit: WalletEdit,
    icon: <FontAwesomeIcon icon={faWallet}/>,
};

export default wallets;
