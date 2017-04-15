<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as EasyAdminController;

/**
 * @method User getUser()
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class AdminController extends EasyAdminController
{
}
