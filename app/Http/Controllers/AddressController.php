<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Block;
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

        if (!$user || !isset($user['Profile']['PublicKeyBase58Check'])) {
            abort(404, 'User not found');
        }

        $page = $request->validated()['page'] ?? 1;

        $transactions = $this->addressService->transactionsByPage($user['Profile']['PublicKeyBase58Check'], $page);

        if (!$transactions) {
            abort(404, "User's transactions loading error");
        }

        $transactionQuantity = $this->addressService->transactionQuantity($user['Profile']['PublicKeyBase58Check']);

        $user = $user['Profile'];

        return view('address', compact(['transactions', 'user', 'transactionQuantity', 'page', 'address']));
    }
}
