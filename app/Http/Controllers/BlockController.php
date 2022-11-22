<?php

namespace App\Http\Controllers;

use App\Services\DesoService;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index($block)
    {
        $desoService = app(DesoService::class);

        $block = $desoService->blockInfoByHeight($block, true);

        if (!$block || !isset($block['Header'])) {
            abort(404, 'Block not found');
        }

        $transactions = $block['Transactions'];
        $block = $block['Header'];

        return view('block', compact(['block', 'transactions']));
    }
}
