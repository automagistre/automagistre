import {TenantState} from '../types'

export const CHANGE_TENANT = 'CHANGE_TENANT'

export const changeTenant = (tenant: TenantState) => ({
    type: CHANGE_TENANT,
    payload: tenant,
})
