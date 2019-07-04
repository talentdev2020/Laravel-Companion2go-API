<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\FAQ;

class FAQController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $code = 200;
        $faqs = FAQ::where(['category' => 'app'])->get();

        return new Response($faqs, $code);
    }

}
