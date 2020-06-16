<?php

declare(strict_types=1);

namespace App;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Roles
{
    public const SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ADMIN = 'ROLE_ADMIN';
    /**
     * Доступ в админку.
     */
    public const EMPLOYEE = 'ROLE_EMPLOYEE';
    public const MAINTENANCE_CONFIGURATOR = 'ROLE_MAINTENANCE_CONFIGURATOR';
    public const CUSTOMER_FEEDBACK = 'ROLE_CUSTOMER_FEEDBACK';
    public const EMPLOYEE_MANAGER = 'ROLE_EMPLOYEE_MANAGER';
    public const USER_MANAGER = 'ROLE_USER_MANAGER';
    /**
     * Доступ к справочникам (Производители, Кузова, Запчасти).
     */
    public const DICTIONARY = 'ROLE_DICTIONARY';
    /**
     * Бухгалтерия.
     */
    public const ACCOUNTING = 'ROLE_ACCOUNTING';
    /**
     * Доступ к клиентам (Люди, Организации, Автомобили).
     */
    public const CUSTOMER = 'ROLE_CUSTOMER';
    /**
     * Доступ к отчётам
     */
    public const REPORT = 'ROLE_REPORT';
    /**
     * Доступ к заказам
     */
    public const ORDER = 'ROLE_ORDER';
    /**
     * Доступ к складу.
     */
    public const STOCK = 'ROLE_STOCK';
}
