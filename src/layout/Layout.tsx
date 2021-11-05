import {useSelector} from 'react-redux';
import {Layout, LayoutProps} from 'react-admin';
import AppBar from './AppBar';
import Menu from './Menu';
import {darkTheme, lightTheme} from './themes';
import {AppState} from '../types';
import {adaptV4Theme} from '@mui/material/styles';

export default (props: LayoutProps) => {
    const theme = useSelector((state: AppState) =>
        state.theme === 'dark' ? darkTheme : lightTheme
    );

    return <Layout {...props} appBar={AppBar} menu={Menu} theme={adaptV4Theme(theme)} />;
};
