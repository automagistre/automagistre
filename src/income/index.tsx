import {faBook} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {EditGuesser} from 'react-admin'
import IncomeEdit from './IncomeEdit'
import IncomeList from './IncomeList'

const income = {
    list: IncomeList,
    create: EditGuesser,
    edit: IncomeEdit,
    icon: <FontAwesomeIcon icon={faBook}/>,
}

export default income
