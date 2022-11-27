<?php

namespace App\Services;

use App\Helpers\TransactionHelper;
use App\Models\User;

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

    public function transactionsUsernames($transactions)
    {
        $userKeys = [];

        foreach($transactions as $transaction) {
            $affectedPublicKeys = $transaction['TransactionMetadata']['AffectedPublicKeys'];

            $transactionInputs = TransactionHelper::getTransferInputs($affectedPublicKeys);

            foreach ($transactionInputs as $transactionInput) {
                $userKeys[] = $transactionInput['PublicKeyBase58Check'];
            }

            $userKeys[] = $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'];
        }

        return User::whereIn('KeyBase58', $userKeys)
            ->where('Username', '!=', '')
            ->get()
            ->pluck('Username', 'KeyBase58');
    }
}
