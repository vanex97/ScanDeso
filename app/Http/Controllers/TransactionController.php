<?php

namespace App\Http\Controllers;

use App\Services\DesoService;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    public $transactionService;

    public function __construct()
    {
        $this->transactionService = app(TransactionService::class);
    }

    public function index($transactionId)
    {
        $transaction = $this->transactionService->transaction($transactionId);

        if (!$transaction) {
            abort(404);
        }

        $block = null;
        if (isset($transaction['BlockHashHex'])) {
            $block = $this->transactionService->blockInfo($transaction['BlockHashHex']);
        }

        $transactorKey = $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'];

        $transactorProfile = $this->transactionService->transactorProfile($transactorKey);

        $lastTransaction = $this->transactionService->lastTransaction($transactorKey);

        $userKeyToUsername = $this->transactionService->transactionsUsernames([$transaction]);

        return view('transaction', compact(['transaction', 'block', 'lastTransaction', 'transactorProfile', 'userKeyToUsername']));
    }
}
