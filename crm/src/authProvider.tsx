import {useKeycloak} from '@react-keycloak/web'
import jwt_decode from 'jwt-decode'
import {AuthProvider} from 'react-admin'

interface JwtToken {
    'https://hasura.io/jwt/claims': {
        'x-hasura-user-id': string,
        'x-hasura-default-role': string,
        'x-hasura-allowed-roles': string[],
    }
}

const useAuthProvider = (): AuthProvider => {
    const {keycloak} = useKeycloak()

    return ({
        login: () => keycloak.login(),
        checkError: () => Promise.resolve(),
        checkAuth: () => {
            return keycloak.authenticated && keycloak.token
                ? Promise.resolve()
                : Promise.reject('Failed to obtain access token.')
        },
        logout: () => 'development' === process.env.NODE_ENV ? Promise.resolve() : keycloak.logout(),
        getIdentity: () => {
            if (keycloak.token) {
                const decoded: any = jwt_decode(keycloak.token)
                const id = decoded.sub
                const fullName = decoded.name
                return Promise.resolve({id, fullName})
            }
            return Promise.reject('Failed to get identity')
        },
        getPermissions: () => {
            let isGrantAccess = false

            if (keycloak.token) {
                const hasuraClaims: JwtToken = jwt_decode(keycloak.token)
                const claims = hasuraClaims['https://hasura.io/jwt/claims']
                const roles = claims['x-hasura-allowed-roles']

                isGrantAccess = roles.includes('admin') || roles.includes('manager')
            }

            return Promise.resolve(isGrantAccess)
        },
    })
}

export default useAuthProvider
