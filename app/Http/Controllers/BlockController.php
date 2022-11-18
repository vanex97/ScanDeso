<?php

namespace App\Http\Controllers;

use App\Services\DesoService;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index($block)
    {
        $desoService = app(DesoService::class);

        $block = $desoService->blockInfoByHeight($block, false);

        if (!$block || !isset($block['Header'])) {
            abort(404);
        }

        $block = $block['Header'];

        return view('block', compact('block'));
    }
}
