<?php

namespace App\Services;

class TransactionService
{
    public $desoService;

    public function __construct()
    {
        $this->desoService = app(DesoService::class);
    }

    public function transaction($transactionId)
    {
        $transaction = $this->desoService->transactionInfo($transactionId);

        return $transaction['Transactions'][0] ?? null;
    }

    public function blockInfo($blockHasHex)
    {
        if (isset($blockHasHex)) {
            $block = $this->desoService->blockInfo($blockHasHex);

            if (isset($block['Header']['BlockHashHex'])) {
                return  $block['Header'];
            }
        }

        return null;
    }

    public function transactorProfile($transactorKey)
    {
        $transactorProfile = $this->desoService->getSingleProfile($transactorKey);

        return $transactorProfile['Profile'] ?? null;

    }

    public function lastTransaction($transactorKey)
    {
        $lastTransaction = $this->desoService->transactionsInfo($transactorKey, 1);

        return $lastTransaction['Transactions'][0]['TransactionIDBase58Check'] ?? null;
    }
}
