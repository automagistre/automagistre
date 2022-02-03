import ExpandMore from '@mui/icons-material/ExpandMore'
import {Collapse, List, ListItemIcon, MenuItem, Tooltip, Typography} from '@mui/material'
import {Theme} from '@mui/material/styles'
import {makeStyles} from '@mui/styles'
import {Fragment, ReactElement, ReactNode} from 'react'
import {ReduxState} from 'react-admin'
import {useSelector} from 'react-redux'

const useStyles = makeStyles((theme: Theme) => ({
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
}))

interface Props {
    dense: boolean;
    handleToggle: () => void;
    icon: ReactElement;
    isOpen: boolean;
    name: string;
    children: ReactNode;
}

const SubMenu = (props: Props) => {
    const {handleToggle, isOpen, name, icon, children, dense} = props
    const classes = useStyles()
    const sidebarIsOpen = useSelector<ReduxState, boolean>(
        state => state.admin.ui.sidebarOpen,
    )

    const header = (
        <MenuItem dense={dense} onClick={handleToggle}>
            <ListItemIcon className={classes.icon}>
                {isOpen ? <ExpandMore/> : icon}
            </ListItemIcon>
            <Typography variant="inherit" color="textSecondary">
                {name}
            </Typography>
        </MenuItem>
    )

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
    )
}

export default SubMenu
