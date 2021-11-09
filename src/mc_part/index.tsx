import {faMap} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {EditGuesser, ListGuesser} from 'react-admin'

const mcParts = {
    list: ListGuesser,
    create: EditGuesser,
    edit: EditGuesser,
    icon: <FontAwesomeIcon icon={faMap}/>,
}

export default mcParts
