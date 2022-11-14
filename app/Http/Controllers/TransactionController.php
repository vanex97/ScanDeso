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

        $block = $desoService->blockInfo($transaction['BlockHashHex']);

        if (!isset($block['Header']['BlockHashHex'])) {
            abort(404);
        }

        $block = $block['Header'];

        $transactionOwner = $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'];
        $lastTransaction = $desoService->transactionsInfo($transactionOwner, 1);

        if (isset($lastTransaction['Transactions'][0]['TransactionIDBase58Check'])) {
            $lastTransaction = $lastTransaction['Transactions'][0]['TransactionIDBase58Check'];
        } else {
            $lastTransaction = null;
        }

//        dd($transaction, $block);

        return view('transaction', compact(['transaction', 'block', 'lastTransaction']));
    }
}
