<?php

namespace PHPSTORM_META {

    use App\Shared\Doctrine\Registry;

    override(Registry::get(), map(0, [
        '' => '@',
    ]));
    override(Registry::findOneBy(), map(0, [
        '' => '@',
    ]));
    override(Registry::getBy(), map(0, [
        '' => '@',
    ]));
    override(\App\EasyAdmin\Controller\AbstractController::getEntity(), map(0, [
        '' => '@',
    ]));
}
