<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    /** @var LINEBot */
    private $lineBot;
    private $lineUserId;

    public function __construct($lineUserId)
    {
        $this->lineUserId = $lineUserId;
        $this->lineBot = app(LINEBot::class);
    }

    public function index()
    {
    	return view('test');
    }
}
