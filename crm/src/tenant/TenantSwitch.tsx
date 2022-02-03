import {gql, useApolloClient} from '@apollo/client'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import {useEffect, useState} from 'react'
import {Title} from 'react-admin'
import {useDispatch} from 'react-redux'
import {TenantState} from '../types'
import {changeTenant} from './actions'

interface Group {
    identifier: string,
    name: string,
    tenants: TenantState[],
}

const TenantSwitch = () => {
    const dispatch = useDispatch()

    const apolloClient = useApolloClient()
    const [groups, setGroups] = useState<Group[]>([])

    useEffect(() => {
        const query = gql`
            query MyQuery {
                groups: tenant_group(order_by: {identifier: asc}) {
                    name
                    tenants(order_by: {identifier: asc}) {
                        id
                        identifier
                        name
                        group: tenant_group {
                            identifier
                        }
                    }
                }
            }
            `

        apolloClient
            .query({query})
            .then(res => setGroups(res.data.groups))
    }, [apolloClient])

    if (!groups) return <p>Загрузка</p>

    return (
        <Card>
            <Title title="Выбор сервиса"/>
            {groups.map((group) => <CardContent key={group.identifier}>
                <div>{group.name}</div>
                {group.tenants.map(tenant => <Button
                    key={tenant.id}
                    variant="contained"
                    onClick={() => dispatch(changeTenant(tenant))}
                >
                    {tenant.name}
                </Button>)}
            </CardContent>)}
        </Card>
    )
}

export default TenantSwitch
