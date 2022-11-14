<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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

//    /**
//     * метод получения цепочки транзакций
//     */
//    public function transactionsChain($transactionId, $limit)
//    {
//        $transactions = [];
//
//        for ($i = 0; $i < $limit; $i++) {
//            $transaction = $this->transactionInfo($transactionId);
//
//            if (!isset($transaction['Transactions'][0])) {
//                return null;
//            }
//
//            $transactions[] = $transaction['Transactions'][0];
//            $transactionId = $transaction['Transactions'][0]['Inputs'][0]['TransactionIDBase58Check'];
//        }
//
//        return $transactions;
//    }

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
