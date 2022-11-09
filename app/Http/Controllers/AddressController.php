<?php

namespace App\Http\Controllers;

use App\Services\DesoService;

class AddressController extends Controller
{
    public const TRANSACTIONS_LIMIT = 2500;

    public function index($address)
    {
        $desoService = app(DesoService::class);

        $user = $desoService->getSingleProfile($address);
        $transactions = null;

        if (isset($user['Profile']['PublicKeyBase58Check'])) {
            $transactions = $desoService->transactionsInfo(
                $user['Profile']['PublicKeyBase58Check'],
                self::TRANSACTIONS_LIMIT
            );
        }

        if (!$transactions) {
            abort(404);
        }

        $transactions = array_reverse($transactions['Transactions']);
        $user = $user['Profile'];

        return view('address', compact(['transactions', 'user']));
    }
}
