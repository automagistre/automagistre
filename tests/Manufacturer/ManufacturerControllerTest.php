<?php

namespace App\Tests\Manufacturer;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ManufacturerControllerTest extends WebTestCase
{
    public function testCreateAndUpdate(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'employee@automagistre.ru',
            'PHP_AUTH_PW' => 'pa$$word',
        ]);

        $client->request('GET', '/msk/?entity=Manufacturer&action=list');
        static::assertTrue($client->getResponse()->isSuccessful());
        static::assertSelectorTextContains('html h1.title', 'Производители');

        $client->clickLink('Создать');
        static::assertTrue($client->getResponse()->isSuccessful());
        static::assertSelectorTextContains('html h1.title', 'Создать нового Прозводителя');

        $client->submitForm('Сохранить изменения', [
            'manufacturer[name]' => 'Toyota',
            'manufacturer[localizedName]' => 'Тойота',
            'manufacturer[logo]' => 'toyota.jpeg',
        ]);

        static::assertTrue($client->getResponse()->isRedirection());
        $crawler = $client->followRedirect();

        static::assertSelectorTextContains('html h1.title', 'Производители');
        $line = $crawler->filter('span[title=Toyota]')->parents()->parents()->first();
        $id = $line->children()->text();

        $client->click($line->filter('a.action-edit')->link());
        static::assertSelectorTextContains('html h1.title', 'Редактировать Производителя');

        $client->submitForm('Сохранить изменения', [
            'manufacturer[name]' => 'Toyota Edited',
            'manufacturer[localizedName]' => 'Тойота Отредактированная',
            'manufacturer[logo]' => 'toyota_edited.jpeg',
        ]);

        static::assertTrue($client->getResponse()->isRedirection());
        $crawler = $client->followRedirect();

        static::assertSelectorTextContains('html h1.title', 'Производители');
        $line = $crawler->filter('span[title="Toyota Edited"]')->parents()->parents()->first();
        static::assertSame($id, $line->children()->text());
    }
}
