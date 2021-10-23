import {Route} from 'react-router-dom';
import Configuration from './configuration/Configuration';
import TenantSwitch from "./tenants/TenantSwitch";

export default [
    <Route exact path="/configuration" render={() => <Configuration/>}/>,
    <Route exact path="/switch" render={() => <TenantSwitch/>}/>,
];
