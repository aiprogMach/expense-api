<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as TestApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\ExpenseFixture;
use App\Repository\ExpenseRepository;

class ExpenseControllerTest extends TestApiTestCase
{
    public function testExpensesListingDefault(): void
    {
        $response = static::createClient()->request('GET', '/expenses');

        $this->assertResponseIsSuccessful();
        $responseArray = json_decode($response->getContent(), true);

        $all = $this->getContainer()->get(ExpenseRepository::class)->findAll();
        static::assertSame(0, $responseArray['page']);
        static::assertSame(100, $responseArray['count_per_page']);
        static::assertSame(count($all), $responseArray['total_count']);
        static::assertSame((int)ceil(count($all) / 100), $responseArray['total_pages']);
        $expenses = $responseArray['expenses'];
        $expense = current($expenses);
        static::assertCount(count($all), $expenses);
        static::assertArrayHasKey('id', $expense);
        static::assertSame('Expense 1', $expense['description']);
        static::assertSame('10.00', $expense['price']);
        static::assertSame('EUR', $expense['currency']);
        static::assertSame(2, $expense['type']);
    }

    public function testExpensesShowSuccess(): void
    {
        $response = static::createClient()->request('GET', '/expenses/1');

        $this->assertResponseIsSuccessful();
        $expense = json_decode($response->getContent(), true);

        static::assertArrayHasKey('id', $expense);
        static::assertSame('Expense 1', $expense['description']);
        static::assertSame('10.00', $expense['price']);
        static::assertSame('EUR', $expense['currency']);
        static::assertSame(2, $expense['type']);
    }

    public function testExpensesCreateInvalidPrice(): void
    {
        $response = static::createClient()->request('POST', '/expenses', [
            'json' => [
                'description' => 'Expense 1',
                'price' => -1,
                'currency' => 'EUR',
                'type' => 2,
            ],
        ]);

        static::assertResponseStatusCodeSame(400);
    }

    public function testExpensesCreateInvalidCurrency(): void
    {
        $response = static::createClient()->request('POST', '/expenses', [
            'json' => [
                'description' => 'Expense 1',
                'price' => '100',
                'currency' => 'EURx',
                'type' => 2,
            ],
        ]);

        static::assertResponseStatusCodeSame(400);
    }

    public function testExpensesCreateInvalidType(): void
    {
        $response = static::createClient()->request('POST', '/expenses', [
            'json' => [
                'description' => 'Expense 1',
                'price' => '100',
                'currency' => 'EUR',
                'type' => 1000,
            ],
        ]);

        static::assertResponseStatusCodeSame(400);
    }

    public function testExpensesCreateSuccess(): void
    {
        $response = static::createClient()->request('POST', '/expenses', [
            'json' => [
                'description' => 'Expense something',
                'price' => '15',
                'currency' => 'EUR',
                'type' => 1,
            ],
        ]);

        static::assertResponseStatusCodeSame(201);

        $responseArray = json_decode($response->getContent(), true);
        static::assertArrayHasKey('id', $responseArray);
        static::assertSame('Expense something', $responseArray['description']);
        static::assertSame('15.00', $responseArray['price']);
        static::assertSame('EUR', $responseArray['currency']);
        static::assertSame(1, $responseArray['type']);
    }

    public function testExpensesUpdateInvalidPrice(): void
    {
        $response = static::createClient()->request('PUT', '/expenses/1', [
            'json' => [
                'description' => 'Expense 1',
                'price' => -1,
                'currency' => 'EUR',
                'type' => 2,
            ],
        ]);

        static::assertResponseStatusCodeSame(400);
    }

    public function testExpensesUpdateNotFound(): void
    {
        $response = static::createClient()->request('PUT', '/expenses/1000000', [
            'json' => [
                'description' => 'Expense 1',
                'price' => -1,
                'currency' => 'EUR',
                'type' => 2,
            ],
        ]);

        static::assertResponseStatusCodeSame(404);
    }

    public function testExpensesDelete(): void
    {
        $expense = current($this->getContainer()->get(ExpenseRepository::class)->findAll());

        $response = static::createClient()->request('DELETE', '/expenses/' . $expense->getId());

        static::assertResponseStatusCodeSame(204);
    }
}
