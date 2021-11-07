import {faBookOpen, faMap, faPiggyBank, faUsers} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {Theme} from '@mui/material/styles'
import {makeStyles} from '@mui/styles'
import classnames from 'classnames'
import {useState} from 'react'
import {DashboardMenuItem, MenuItemLink, MenuProps, ReduxState} from 'react-admin'
import {useSelector} from 'react-redux'
import contacts from '../contacts'
import legalForms from '../legal_forms'

import manufacturers from '../manufacturers'
import mcEquipments from '../mc_equipments'
import mcWorks from '../mc_works'
import orders from '../orders'
import parts from '../parts'
import {AppState} from '../types'
import vehicles from '../vehicle_bodies'
import wallets from '../wallets'

import SubMenu from './SubMenu'

type MenuName = 'menuCatalog' | 'menuFinance' | 'menuCustomers' | 'menuMc';

const Menu = ({dense = false}: MenuProps) => {
    const tenant = useSelector((state: AppState) => state.tenant)
    const [state, setState] = useState({
        menuCatalog: true,
        menuFinance: true,
        menuCustomers: true,
        menuMc: true,
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
                    pathname: '/orders',
                    state: {_scrollToTop: true},
                }}
                primaryText="Заказы"
                leftIcon={orders.icon}
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
                    leftIcon={wallets.icon}
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
                    leftIcon={contacts.icon}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/vehicle',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Автомобили"
                    leftIcon={vehicles.icon}
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
                    leftIcon={legalForms.icon}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/manufacturer',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Производители"
                    leftIcon={manufacturers.icon}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/vehicle_body',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Кузова"
                    leftIcon={vehicles.icon}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/part',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Запчасти"
                    leftIcon={parts.icon}
                    dense={dense}
                />
            </SubMenu>
            <SubMenu
                handleToggle={() => handleToggle('menuMc')}
                isOpen={state.menuMc}
                name="Справочники"
                icon={<FontAwesomeIcon icon={faMap}/>}
                dense={dense}
            >
                <MenuItemLink
                    to={{
                        pathname: '/mc_work',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Работы"
                    leftIcon={mcWorks.icon}
                    dense={dense}
                />
                <MenuItemLink
                    to={{
                        pathname: '/mc_equipment',
                        state: {_scrollToTop: true},
                    }}
                    primaryText="Комплектации"
                    leftIcon={mcEquipments.icon}
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
        width: 200,
    },
    closed: {
        width: 55,
    },
}))

export default Menu
