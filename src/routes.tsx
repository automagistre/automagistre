import {Route} from 'react-router-dom'
import Configuration from './configuration/Configuration'
import TenantSwitch from './tenants/TenantSwitch'

const customRoutes = [
    <Route exact path="/configuration" render={() => <Configuration/>}/>,
    <Route exact path="/switch" render={() => <TenantSwitch/>}/>,
]

export default customRoutes
