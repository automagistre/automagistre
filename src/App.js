import React, {useEffect, useState} from 'react';
import buildHasuraProvider from 'ra-data-hasura';
import {AdminContext, AdminUI, Resource, useRedirect} from 'react-admin';
import polyglotI18nProvider from 'ra-i18n-polyglot';
import russianMessages from 'ra-language-russian';
import {ApolloClient, ApolloProvider, createHttpLink, gql, InMemoryCache, useApolloClient} from '@apollo/client';
import Keycloak from "keycloak-js";
import useAuthProvider from "./authProvider";
import {ReactKeycloakProvider} from "@react-keycloak/web";
import {setContext} from "@apollo/client/link/context";
import {Dashboard} from "./dashboard/Dashboard";
import manufacturers from './manufacturers';
import vehicles from './vehicles';
import parts from './parts';
import tenants from './tenants';
import wallets from './wallets';
import contacts from './contacts';
import legalForms from './legal_forms';
import {Layout} from "./layout";
import themeReducer from './themeReducer';
import customRoutes from './routes';
import {useDispatch, useSelector} from "react-redux";
import {CHANGE_TENANT, changeTenant} from "./tenant/actions";

let keycloakConfig = {
    url: 'https://sso.automagistre.ru/auth',
    realm: 'automagistre',
    clientId: 'crm-next-oauth',
    onLoad: "login-required",
};

const keycloak = Keycloak(keycloakConfig);

const onTokenExpired = () => {
    keycloak
        .updateToken(30)
        .catch(() => {
            console.error("failed to refresh token");
        });
};

const i18nProvider = polyglotI18nProvider(() => russianMessages, 'ru');


const Resources = () => {
    const tenant = useSelector(state => state.tenant)
    const redirect = useRedirect();
    const apolloClient = useApolloClient();
    const dispatch = useDispatch();

    useEffect(() => {
        if (tenant) {
            return
        }

        const pair = window.location.pathname.split('/').slice(1, 3);
        if (pair.length === 2) {
            const [groupIdentifier, tenantIdentifier] = pair

            const GET_TENANT = gql`
                query Tenant($tenant: String!, $group: String!) {
                    tenant(where: {identifier: {_eq: $tenant}, tenant_group: {identifier: {_eq: $group}}}) {
                        id
                        identifier
                        name
                        group: tenant_group {
                            id
                            identifier
                            name
                            }
                    }
                }
            `

            apolloClient
                .query({query: GET_TENANT, variables: {tenant: tenantIdentifier, group: groupIdentifier}})
                .then((res) => {
                    const tenantFromUrl = res.data.tenant[0]

                    if (tenantFromUrl) {
                        dispatch(changeTenant(tenantFromUrl))
                    } else {
                        redirect('/switch')
                    }
                })
        } else {
            redirect('/switch')
        }
    }, [apolloClient, dispatch, redirect, tenant])

    return (
        <AdminUI
            customRoutes={customRoutes}
            layout={Layout}
        >
            <Resource name="contact" {...contacts}/>
            <Resource name="contact_reference"/>
            <Resource name="legal_form" {...legalForms}/>
            <Resource name="legal_form_type"/>
            <Resource name="manufacturer" {...manufacturers} />
            <Resource name="part" {...parts} />
            <Resource name="tenant" {...tenants}/>
            <Resource name="unit"/>
            <Resource name="vehicle" {...vehicles} />
            <Resource name="wallet" {...wallets} />
        </AdminUI>
    )
}

const AdminWithKeycloak = () => {
    const keycloakAuthProvider = useAuthProvider();

    const [dataProvider, setDataProvider] = useState(null);
    const [tenant, setTenant] = useState(null);
    const [apollo, setApollo] = useState(null);

    useEffect(() => {
        const httpLink = createHttpLink({
            uri: window.location.protocol
                + '//api-next.'
                + window.location.hostname.split('.').splice(-2).join('.')
                + '/v1/graphql',
        });

        const authLink = setContext((_, {headers}) => {
            return {
                headers: {
                    ...headers,
                    ...(keycloak.token && {authorization: `Bearer ${keycloak.token}`}),
                    'X-Hasura-Role': 'manager',
                    ...(tenant && {'X-Hasura-Tenant-Id': tenant.id}),
                },
            }
        });

        const clientWithAuth = new ApolloClient({
            link: authLink.concat(httpLink),
            cache: new InMemoryCache(),
        });

        (async () => {
            const dataProvider = await buildHasuraProvider({
                client: clientWithAuth,
            });
            setApollo(clientWithAuth)
            setDataProvider(() => dataProvider);
        })();
    }, [tenant]);

    if (!dataProvider) return <p>Загрузка...</p>;

    return (
        <ApolloProvider client={apollo}>
            <AdminContext
                disableTelemetry
                dashboard={Dashboard}
                title="Автомагистр - CRM"
                customReducers={{
                    theme: themeReducer,
                    tenant: (previousState = null, action) => {
                        if (action.type === CHANGE_TENANT) {
                            setTimeout(() => setTenant(action.payload), 0)

                            window.history.replaceState(null, "Автомагистр - CRM ", `/${action.payload.group.identifier}/${action.payload.identifier}`)

                            return action.payload;
                        }

                        return previousState;
                    },
                }}
                dataProvider={dataProvider}
                authProvider={keycloakAuthProvider}
                i18nProvider={i18nProvider}
            >
                <Resources/>
            </AdminContext>
        </ApolloProvider>
    );
};

const App = () => {
    return (
        <ReactKeycloakProvider
            authClient={keycloak}
            LoadingComponent={<div></div>}
            initOptions={keycloakConfig}
            onTokenExpired={onTokenExpired}
        >
            <AdminWithKeycloak/>
        </ReactKeycloakProvider>
    );
};

export default App;
