import {Fragment, ReactElement, ReactNode} from 'react';
import {useSelector} from 'react-redux';
import {Collapse, List, ListItemIcon, MenuItem, Tooltip, Typography,} from '@material-ui/core';
import {makeStyles} from '@material-ui/core/styles';
import ExpandMore from '@material-ui/icons/ExpandMore';
import {ReduxState} from 'react-admin';

const useStyles = makeStyles(theme => ({
    icon: {minWidth: theme.spacing(5)},
    sidebarIsOpen: {
        '& a': {
            transition: 'padding-left 195ms cubic-bezier(0.4, 0, 0.6, 1) 0ms',
            paddingLeft: theme.spacing(4),
        },
    },
    sidebarIsClosed: {
        '& a': {
            transition: 'padding-left 195ms cubic-bezier(0.4, 0, 0.6, 1) 0ms',
            paddingLeft: theme.spacing(2),
        },
    },
}));

interface Props {
    dense: boolean;
    handleToggle: () => void;
    icon: ReactElement;
    isOpen: boolean;
    name: string;
    children: ReactNode;
}

const SubMenu = (props: Props) => {
    const {handleToggle, isOpen, name, icon, children, dense} = props;
    const classes = useStyles();
    const sidebarIsOpen = useSelector<ReduxState, boolean>(
        state => state.admin.ui.sidebarOpen
    );

    const header = (
        <MenuItem dense={dense} button onClick={handleToggle}>
            <ListItemIcon className={classes.icon}>
                {isOpen ? <ExpandMore/> : icon}
            </ListItemIcon>
            <Typography variant="inherit" color="textSecondary">
                {name}
            </Typography>
        </MenuItem>
    );

    return (
        <Fragment>
            {sidebarIsOpen || isOpen ? (
                header
            ) : (
                <Tooltip title={name} placement="right">
                    {header}
                </Tooltip>
            )}
            <Collapse in={isOpen} timeout="auto" unmountOnExit>
                <List
                    dense={dense}
                    component="div"
                    disablePadding
                    className={
                        sidebarIsOpen
                            ? classes.sidebarIsOpen
                            : classes.sidebarIsClosed
                    }
                >
                    {children}
                </List>
            </Collapse>
        </Fragment>
    );
};

export default SubMenu;
