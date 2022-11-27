<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\User;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index()
    {
        $lastParsedBlock = Block::latest('Height')->first();

        if (!$lastParsedBlock) {
            abort(404);
        }
        $lastParsedBlock = $lastParsedBlock->Height;

        $usersParsed = User::count();
        $userNamesParsed = User::where('Username', '!=', '')->count();

        return view('info', compact(['lastParsedBlock', 'usersParsed', 'userNamesParsed']));
    }
}
