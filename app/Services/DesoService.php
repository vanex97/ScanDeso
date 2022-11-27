<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;

class DesoService
{
    public const BASE_58_STARTS_WITH = 'BC';

    public const TRANSACTIONS_LIMIT = 25;

    /** @var Client */
    public $client;

    public function __construct()
    {
        $this->client = new Client(
            [
                'base_uri' => config('app.deso.api_base_url'),
                'headers' => [
                    'Content-Type'  => 'application/json; charset=utf-8',
                ]
            ]
        );
    }

    public function getExchangeRate()
    {
        return $this->request('GET', 'v0/get-exchange-rate');
    }

    /**
     *
     * ExtraData -> DAOPublicKeysPurchased список дао коинов пользователя
     */
    public function getSingleProfile($usernameOrPublicKey)
    {
        $user = null;

        if (str_starts_with($usernameOrPublicKey, self::BASE_58_STARTS_WITH)) {
            $user = $this->request('POST', 'v0/get-single-profile', [
                'PublicKeyBase58Check' => $usernameOrPublicKey
            ]);
        }

        if (!$user) {
            $user = $this->request('POST', 'v0/get-single-profile', [
                'Username' => $usernameOrPublicKey
            ]);
        }

        return $user;
    }

    /**
     * Поля
     * TransactionIDBase58Check хэш транзакции
     * TransactionType тип транзакции (например LIKE, BASIC_TRANSFER)
     * TransactionMetadata => TxnOutputs -
     *  0 index -> Получатель и количество (PublicKey, AmountNanos)
     *  1 index -> Отправитель и его текущий баланс (PublicKey, AmountNanos)
     * FreeNanos комис
     */

    public function transactionInfoByPage($publicKeyBase58Check, $limit, $page = 1)
    {
        $offset = 0;

        if ($page != 1) {
            $offset = ($page - 1) * DesoService::TRANSACTIONS_LIMIT;

            $offset = $this->transactionQuantity($publicKeyBase58Check) - $offset;
        }

        return $this->transactionsInfo($publicKeyBase58Check, $limit, $offset);
    }

    public function transactionsInfo($publicKeyBase58Check, $limit, $offset = null)
    {
        return $this->request('POST','v1/transaction-info', [
            'PublicKeyBase58Check' => $publicKeyBase58Check,
            'Limit' => $limit,
            'LastPublicKeyTransactionIndex' => $offset
        ]);
    }

    public function transactionQuantity($publicKeyBase58Check)
    {
        $response = $this->request('POST','v1/transaction-info', [
            'PublicKeyBase58Check' => $publicKeyBase58Check,
            'IDsOnly' => true,
            'Limit' => 1,
        ]);

        if (isset($response['LastPublicKeyTransactionIndex'])) {
            return $response['LastPublicKeyTransactionIndex'];
        }

        return null;
    }

    public function transactionInfo($transactionID)
    {
        return $this->request('POST','v1/transaction-info', [
            'TransactionIDBase58Check' => $transactionID
        ]);
    }

    public function blockInfo($hashHex, $fullBlock = false)
    {
        return $this->request('POST','v1/block', [
            'HashHex' => $hashHex,
            'FullBlock' => $fullBlock
        ]);
    }

    public function blockInfoByHeight($height, $fullBlock = false)
    {
        return $this->request('POST','v1/block', [
            'Height' => (integer) $height,
            'FullBlock' => $fullBlock
        ]);
    }

    public function blockInfoByHeightAsync($heightList, $concurrency, $callback)
    {
        $promises = (function () use ($heightList) {
            foreach ($heightList as $height) {
                yield $this->client->requestAsync('POST', 'v1/block', ['body' => json_encode([
                    'Height' => (integer) $height,
                    'FullBlock' => true
                ])]);
            }
        })();

        $eachPromise = new EachPromise($promises, [
            'concurrency' => $concurrency,
            'fulfilled' => function (Response $response) use ($callback) {
                if ($response->getStatusCode() == 200) {
                    $block = json_decode($response->getBody(), true);
                    call_user_func($callback, $block);
                }
            },
            'rejected' => function ($reason) {}
        ]);

        $eachPromise->promise()->wait();
    }

    private function request($method, $uri, $body = []) {
        try {
            $response = $this->client->request($method,$uri, ['body' => json_encode($body)]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
//            \Log::info('Deso api exception: ' . $e->getMessage());
            return [];
        }
    }
}
