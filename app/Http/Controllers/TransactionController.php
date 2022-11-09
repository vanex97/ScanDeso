<?php

namespace App\Http\Controllers;

use App\Services\DesoService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index($transactionId)
    {
        $desoService = app(DesoService::class);

        dd($desoService->blockInfo($transactionId));
    }
}
