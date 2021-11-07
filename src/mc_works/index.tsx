import {faMap} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {EditGuesser} from 'react-admin'
import McWorkList from './McWorkList'

const mcWorks = {
    list: McWorkList,
    create: EditGuesser,
    edit: EditGuesser,
    icon: <FontAwesomeIcon icon={faMap}/>,
}

export default mcWorks
