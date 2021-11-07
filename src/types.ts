import {Identifier, Record, ReduxState} from 'react-admin'

export type ThemeName = 'light' | 'dark';

export type UUID = string;

export interface TenantState {
    id: UUID,
    identifier: string,
    name: string,
    group: {
        id: UUID,
        name: string,
        identifier: string,
    },
}

export interface AppState extends ReduxState {
    theme: ThemeName;
    tenant: TenantState,
}

export interface Timestampable {
    updated_at: string,
    created_at: string,
}

export interface Commentable {
    comment?: string,
}

export interface Manufacturer extends Record, Timestampable {
    name: string,
    localized_name: string,
}

export interface VehicleBody extends Record, Timestampable {
    name: string,
    localized_name: string,
    manufacturer_id: Identifier,
    case_name: string,
    year_from: number,
    year_till: number,
}

export interface VehicleBodyType extends Record {
    name: string,
}

export interface Part extends Record {
    name: string,
    number: string,
    manufacturer_id: Identifier,
}

export interface Wallet extends Record {
    name: string,
    code: string,
    parent_id: Identifier,
}

export interface LegalForm extends Record {
    short_name: string,
    full_name: string,
    type: Identifier,
}

export interface ContactName {
    type: string,
}

export interface ContactPersonName extends ContactName {
    lastname?: string,
    firstname?: string,
    middlename?: string,
}

export interface ContactOrganizationName extends ContactName {
    name?: string,
    full_name?: string,
}

export interface Contact extends Record, Timestampable {
    legal_form: Identifier,
    name: ContactName,
    telephone: string,
    email: string,
    contractor: boolean,
    supplier: boolean,
    requisites: object,
}

export interface ContactRelation extends Record, Timestampable, Commentable {
    source_id: Identifier,
    target_id: Identifier,
}

export interface Vehicle extends Record, Timestampable, Commentable {
    vehicle_id: Identifier,
    identifier: string,
    year: number,
    body_type: Identifier,
    mileage: number,
    legal_plate: string,
    engine_name: string,
    engine_capacity: string,
    transmission: Identifier,
    drive_wheel: Identifier,
}

export interface Order extends Record, Timestampable, Commentable {
    vehicle_id: Identifier,
    number: string,
    mileage: number,
    status_id: Identifier,
    contact_gave_id: Identifier,
    contact_took_id: Identifier,
    contact_paid_id: Identifier,
}

export interface OrderStatus extends Record {
    name: string,
    color: 'primary' | 'secondary' | 'success' | 'error' | 'info' | 'warning',
}
