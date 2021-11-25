import {
    faBook,
    faBookOpen,
    faCar,
    faCogs,
    faCoins,
    faFileInvoiceDollar,
    faGavel,
    faHandHoldingUsd,
    faIndustry,
    faMap,
    faPiggyBank,
    faRecycle,
    faUsers,
    faWallet,
    faWarehouse,
} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {Theme} from '@mui/material/styles'
import {makeStyles} from '@mui/styles'
import classnames from 'classnames'
import {useState} from 'react'
import {DashboardMenuItem, MenuItemLink, MenuProps, ReduxState} from 'react-admin'
import {useSelector} from 'react-redux'
import {AppState} from '../types'

import SubMenu from './SubMenu'

type MenuName = 'menuCatalog' | 'menuFinance' | 'menuCustomers' | 'menuMc' | 'menuStorage';

const Menu = ({dense = false}: MenuProps) => {
    const tenant = useSelector((state: AppState) => state.tenant)
    const [state, setState] = useState({
        menuCatalog: true,
        menuFinance: true,
        menuCustomers: true,
        menuMc: true,
        menuStorage: true,
    })
    const open = useSelector((state: ReduxState) => state.admin.ui.sidebarOpen)
    useSelector((state: AppState) => state.theme) // force rerender on theme change
    const classes = useStyles()

    const handleToggle = (menu: MenuName) => {
        setState(state => ({...state, [menu]: !state[menu]}))
    }

    if (!tenant) {
        return <></>
    }

    return (
        <div
            className={classnames(classes.root, {
                [classes.open]: open,
                [classes.closed]: !open,
            })}
        >
            {' '}
            <DashboardMenuItem/>
            <MenuItemLink
                to={{
                    pathname: '/order',
                    state: {_scrollToTop: true},
                }}
                primaryText="Заказы"
                leftIcon={<FontAwesomeIcon icon={faFileInvoiceDollar} size="lg" style={{marginLeft: '3px'}}/>}
                exact
            />
            <SubMenu
                handleToggle={() => handleToggle('menuFinance')}
                isOpen={state.menuFinance}
                name="Финансы"
                icon={<FontAwesomeIcon icon={faPiggyBank}/>}
                dense={dense}
            >
                <MenuItemLink
                    to={{
                        pathname: '/wallet',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Счета"
                    leftIcon={<FontAwesomeIcon icon={faWallet}/>}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/wallet_expense',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Расходы"
                    leftIcon={<FontAwesomeIcon icon={faHandHoldingUsd}/>}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/money_transfer',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Проводки"
                    leftIcon={<FontAwesomeIcon icon={faCoins}/>}
                    dense={dense}
                />
            </SubMenu>
            <SubMenu
                handleToggle={() => handleToggle('menuStorage')}
                isOpen={state.menuStorage}
                name="Склад"
                icon={<FontAwesomeIcon icon={faWarehouse}/>}
                dense={dense}
            >
                <MenuItemLink
                    to={{
                        pathname: '/part_transfer',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Движения"
                    leftIcon={<FontAwesomeIcon icon={faRecycle}/>}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/income',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Приходы"
                    leftIcon={<FontAwesomeIcon icon={faBook}/>}
                    dense={dense}
                />
            </SubMenu>
            <SubMenu
                handleToggle={() => handleToggle('menuCustomers')}
                isOpen={state.menuCustomers}
                name="Клиенты"
                icon={<FontAwesomeIcon icon={faUsers}/>}
                dense={dense}
            >
                <MenuItemLink
                    to={{
                        pathname: '/contact',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Контакты"
                    leftIcon={<FontAwesomeIcon icon={faUsers}/>}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/vehicle',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Автомобили"
                    leftIcon={<FontAwesomeIcon icon={faCar}/>}
                    dense={dense}
                />
            </SubMenu>
            <SubMenu
                handleToggle={() => handleToggle('menuCatalog')}
                isOpen={state.menuCatalog}
                name="Справочники"
                icon={<FontAwesomeIcon icon={faBookOpen}/>}
                dense={dense}
            >
                <MenuItemLink
                    to={{
                        pathname: '/legal_form',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Правовые формы"
                    leftIcon={<FontAwesomeIcon icon={faGavel}/>}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/manufacturer',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Производители"
                    leftIcon={<FontAwesomeIcon icon={faIndustry}/>}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/vehicle_body',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Кузова"
                    leftIcon={<FontAwesomeIcon icon={faCar}/>}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/part',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Запчасти"
                    leftIcon={<FontAwesomeIcon icon={faCogs}/>}
                    dense={dense}
                />
            </SubMenu>
            <SubMenu
                handleToggle={() => handleToggle('menuMc')}
                isOpen={state.menuMc}
                name="Калькулятор"
                icon={<FontAwesomeIcon icon={faMap}/>}
                dense={dense}
            >
                <MenuItemLink
                    to={{
                        pathname: '/mc_work',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Работы"
                    leftIcon={<FontAwesomeIcon icon={faMap}/>}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/mc_equipment',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Комплектации"
                    leftIcon={<FontAwesomeIcon icon={faMap}/>}
                    dense={dense}
                />
            </SubMenu>
        </div>
    )
}

const useStyles = makeStyles((theme: Theme) => ({
    root: {
        marginTop: theme.spacing(1),
        marginBottom: theme.spacing(1),
        transition: theme.transitions.create('width', {
            easing: theme.transitions.easing.sharp,
            duration: theme.transitions.duration.leavingScreen,
        }),
    },
    open: {
        width: 210,
    },
    closed: {
        width: 50,
    },
}))

export default Menu
