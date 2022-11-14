<?php

namespace App\Http\Controllers;

use App\Services\DesoService;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index($block)
    {
        $desoService = app(DesoService::class);

        dd($desoService->blockInfoByHeight($block, false));
    }
}
