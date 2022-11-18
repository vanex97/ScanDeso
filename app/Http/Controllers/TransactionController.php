<?php

namespace App\Http\Controllers;

use App\Services\DesoService;

class TransactionController extends Controller
{
    public function index($transactionId)
    {
        $desoService = app(DesoService::class);

        $transaction = $desoService->transactionInfo($transactionId);

        if (!isset($transaction['Transactions'][0])) {
            abort(404);
        }

        $transaction = $transaction['Transactions'][0];

        $block = null;

        if (isset($transaction['BlockHashHex'])) {
            $block = $desoService->blockInfo($transaction['BlockHashHex']);

            if (isset($block['Header']['BlockHashHex'])) {
                $block = $block['Header'];
            }
        }

        $transactorKey = $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'];
        $lastTransaction = $desoService->transactionsInfo($transactorKey, 1);

        $transactorProfile = $desoService->getSingleProfile($transactorKey);

        if (isset($transactorProfile['Profile'])) {
            $transactorProfile = $transactorProfile['Profile'];
        }

        if (isset($lastTransaction['Transactions'][0]['TransactionIDBase58Check'])) {
            $lastTransaction = $lastTransaction['Transactions'][0]['TransactionIDBase58Check'];
        } else {
            $lastTransaction = null;
        }

        return view('transaction', compact(['transaction', 'block', 'lastTransaction', 'transactorProfile']));
    }
}
