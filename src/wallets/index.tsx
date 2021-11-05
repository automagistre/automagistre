import {faWallet} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import WalletCreate from './WalletCreate'
import WalletEdit from './WalletEdit'
import WalletList from './WalletList'

const wallets = {
    list: WalletList,
    create: WalletCreate,
    edit: WalletEdit,
    icon: <FontAwesomeIcon icon={faWallet}/>,
}

export default wallets
