import {faBook} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import IncomeCreate from './IncomeCreate'
import IncomeEdit from './IncomeEdit'
import IncomeList from './IncomeList'

const income = {
    list: IncomeList,
    create: IncomeCreate,
    edit: IncomeEdit,
    icon: <FontAwesomeIcon icon={faBook}/>,
}

export default income
