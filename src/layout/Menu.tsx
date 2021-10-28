import {useState} from 'react';
import {useSelector} from 'react-redux';
import {makeStyles} from '@material-ui/core/styles';
import classnames from 'classnames';
import {DashboardMenuItem, MenuItemLink, MenuProps, ReduxState,} from 'react-admin';
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

import manufacturers from '../manufacturers';
import vehicles from '../vehicles';
import parts from '../parts';
import wallets from '../wallets';

import SubMenu from './SubMenu';
import {AppState} from '../types';
import {faBookOpen, faPiggyBank} from "@fortawesome/free-solid-svg-icons";

type MenuName = 'menuCatalog' | 'menuFinance' | 'menuCustomers';

const Menu = ({dense = false}: MenuProps) => {
    const tenant = useSelector((state: AppState) => state.tenant);
    const [state, setState] = useState({
        menuCatalog: true,
        menuFinance: true,
        menuCustomers: true,
    });
    const open = useSelector((state: ReduxState) => state.admin.ui.sidebarOpen);
    useSelector((state: AppState) => state.theme); // force rerender on theme change
    const classes = useStyles();

    const handleToggle = (menu: MenuName) => {
        setState(state => ({...state, [menu]: !state[menu]}));
    };

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
                handleToggle={() => handleToggle('menuCatalog')}
                isOpen={state.menuCatalog}
                name="Справочники"
                icon={<FontAwesomeIcon icon={faBookOpen}/>}
                dense={dense}
            >
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
                        pathname: '/vehicle',
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
        </div>
    );
};

const useStyles = makeStyles(theme => ({
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
}));

export default Menu;
