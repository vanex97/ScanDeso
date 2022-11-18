<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Services\AddressService;
use App\Services\DesoService;

class AddressController extends Controller
{
    public $addressService;

    public function __construct()
    {
        $this->addressService = app(AddressService::class);
    }

    public function index($address, AddressRequest $request)
    {
        $desoService = app(DesoService::class);

        $user = $desoService->getSingleProfile($address);

        $transactions = null;

        $params = $request->validated();

        // pagination
        $page = $params['page'] ?? 1;

        if (isset($user['Profile']['PublicKeyBase58Check'])) {
            $transactions = $desoService->transactionInfoByPage(
                $user['Profile']['PublicKeyBase58Check'],
                DesoService::TRANSACTIONS_LIMIT,
                $page
            );
        }

        if (!$transactions) {
            abort(404);
        }

        $transactionQuantity = $transactions['LastPublicKeyTransactionIndex'] + DesoService::TRANSACTIONS_LIMIT * $page;

        $transactions = array_reverse($transactions['Transactions']);
        $user = $user['Profile'];

        return view('address', compact(['transactions', 'user', 'transactionQuantity', 'page', 'address']));
    }
}
