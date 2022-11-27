<?php

namespace App\Services;

class AddressService
{
    public $desoService;

    public function __construct()
    {
        $this->desoService = app(DesoService::class);
    }

    public function transactionsByPage($userPublicKey, $page)
    {
        $transactions = $this->desoService->transactionInfoByPage(
            $userPublicKey,
            DesoService::TRANSACTIONS_LIMIT,
            $page
        );

        return isset($transactions['Transactions']) ? array_reverse($transactions['Transactions']) : null;
    }

    public function transactionQuantity($userPublicKey)
    {
        $lastTransaction = $this->desoService->transactionsInfo($userPublicKey, 1);

        if (isset($lastTransaction['LastPublicKeyTransactionIndex'])) {
            return $lastTransaction['LastPublicKeyTransactionIndex'] + count($lastTransaction['Transactions']);
        }

        return null;
    }

}
