<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function check()
    {
        return view('check');
    }

    public function test()
    {

        $question = \App\Models\Question::where('custom_type', 'location')->first();
        dump($question->questions);
        die();

        die();

        $question = \App\Models\Question::first();
        dump($question->getParams());
        $question->setParams(['tiles' => 0, 'list' => false, 'extra' => 'zxc']);
        dump($question->getParams());
        $question->save();
        $question = \App\Models\Question::first();
        dump($question->getParams());
        die();
    }
}
