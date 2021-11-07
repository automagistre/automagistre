import {Layout, LayoutProps} from 'react-admin'
import {useSelector} from 'react-redux'
import {AppState} from '../types'
import AppBar from './AppBar'
import Menu from './Menu'
import {darkTheme, lightTheme} from './themes'

const MyLayout = (props: LayoutProps) => {
    const theme = useSelector((state: AppState) =>
        state.theme === 'dark' ? darkTheme : lightTheme,
    )

    // @ts-ignore TODO theme deprecations
    return <Layout {...props} appBar={AppBar} menu={Menu} theme={theme}/>
}

export default MyLayout
