<?php

namespace MeSomb\Operation;

use MeSomb\MeSomb;
use PHPUnit\Framework\TestCase;

class WalletOperationTest extends TestCase
{
    private $providerKey = 'a1dc7a7391c538788043';
    private $accessKey = 'c6c40b76-8119-4e93-81bf-bfb55417b392';
    private $secretKey = 'fe8c2445-810f-4caa-95c9-778d51580163';

    protected function setUp(): void
    {
        MeSomb::$apiBase = 'http://127.0.0.1:8000';
    }

    public function testCreateNewWalletWithSuccess()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);

        $wallet = $client->createWallet([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'contact@gmail.com',
            'phone_number' => '+237677550000',
            'country' => 'CM',
            'gender' => 'MAN',
        ]);
        $this->assertEquals('John', $wallet->first_name);
        $this->assertEquals('Doe', $wallet->last_name);
        $this->assertEquals('contact@gmail.com', $wallet->email);
        $this->assertEquals('+237677550000', $wallet->phone_number);
        $this->assertEquals('CM', $wallet->country);
        $this->assertEquals('MAN', $wallet->gender);
    }

    public function testCreateNewWalletWithMinInfoSuccess()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);

        $wallet = $client->createWallet([
            'last_name' => 'Doe',
            'phone_number' => '+237677550000',
            'gender' => 'MAN',
        ]);
        $this->assertNotNull($wallet->id);
        $this->assertEquals('Doe', $wallet->last_name);
        $this->assertEquals('+237677550000', $wallet->phone_number);
        $this->assertEquals('CM', $wallet->country);
        $this->assertEquals('MAN', $wallet->gender);
    }

    public function testUpdateNewWalletWithMinInfoSuccess()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);

        $wallet = $client->updateWallet(229, [
            'last_name' => 'Doe',
            'phone_number' => '+237677550099',
            'gender' => 'WOMAN',
        ]);
        $this->assertEquals($wallet->id, 229);
        $this->assertEquals('Doe', $wallet->last_name);
        $this->assertEquals('+237677550099', $wallet->phone_number);
        $this->assertEquals('CM', $wallet->country);
        $this->assertEquals('WOMAN', $wallet->gender);
    }

    public function testCreateGetWalletWithSuccess()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);

        $wallet = $client->getWallet(228);
        $this->assertNotNull($wallet->id);
        $this->assertEquals('John', $wallet->first_name);
        $this->assertEquals('Doe', $wallet->last_name);
        $this->assertEquals('contact@gmail.com', $wallet->email);
        $this->assertEquals('+237677550000', $wallet->phone_number);
        $this->assertEquals('CM', $wallet->country);
        $this->assertEquals('MAN', $wallet->gender);
    }

    public function testCreateGetPaginatedWalletsWithSuccess()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);
        $wallets = $client->getWallets();

        $this->assertGreaterThan(0, $wallets->count);
        $this->assertNull($wallets->previous);
        $this->assertGreaterThan( 0, count($wallets->results));
    }

    public function testShouldAddMoneyToWallet()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);
        $wallet = $client->getWallet(228);
        $transaction = $client->addMoney($wallet->id, 1000);

        $this->assertEquals(1, $transaction->direction);
        $this->assertEquals('SUCCESS', $transaction->status);
        $this->assertEquals(1000, $transaction->amount);
        $this->assertEquals((($wallet->balance ?? 0) + 1000), $transaction->balance_after);
        $this->assertEquals(228, $transaction->wallet);
        $this->assertEquals('CM', $transaction->country);
        $this->assertNotNull($transaction->fin_trx_id);
        $this->assertNotNull($transaction->date);
    }

    public function testShouldRemoveMoneyToWallet()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);
        $wallet = $client->getWallet(228);
        $transaction = $client->removeMoney($wallet->id, 1000);

        $this->assertEquals(-1, $transaction->direction);
        $this->assertEquals('SUCCESS', $transaction->status);
        $this->assertEquals(1000, $transaction->amount);
        $this->assertEquals((($wallet->balance ?? 0) - 1000), $transaction->balance_after);
        $this->assertEquals(228, $transaction->wallet);
        $this->assertEquals('CM', $transaction->country);
        $this->assertNotNull($transaction->fin_trx_id);
        $this->assertNotNull($transaction->date);
    }

    public function testShouldGetTransactionDetails()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);
        $transaction = $client->getTransaction(3061);

        $this->assertEquals(3061, $transaction->id);
        $this->assertEquals(-1, $transaction->direction);
        $this->assertEquals('SUCCESS', $transaction->status);
        $this->assertEquals(1000, $transaction->amount);
        $this->assertEquals(1000, $transaction->balance_after);
        $this->assertEquals(228, $transaction->wallet);
        $this->assertEquals('CM', $transaction->country);
        $this->assertNotNull($transaction->fin_trx_id);
        $this->assertNotNull($transaction->date);
    }

    public function testShouldListTransactionsWithSuccess()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);
        $transactions = $client->listTransactions(1);

        $this->assertGreaterThan(0, $transactions->count);
        $this->assertNull($transactions->previous);
        $this->assertGreaterThan( 0, count($transactions->results));
    }

    public function testShouldListTransactionsForWalletWithSuccess()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);
        $transactions = $client->listTransactions(1, 228);

        $this->assertGreaterThan(0, $transactions->count);
        $this->assertNull($transactions->previous);
        $this->assertGreaterThan( 0, count($transactions->results));
    }

    public function testShouldGetTransactionsWithSuccess()
    {
        $client = new WalletOperation($this->providerKey, $this->accessKey, $this->secretKey);

        $transactions = $client->getTransactions([620665]);
        $this->assertGreaterThan(0, count($transactions));
    }
}