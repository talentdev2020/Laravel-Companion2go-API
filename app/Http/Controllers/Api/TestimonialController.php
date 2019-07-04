<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\TestimonialCreateRequest $request)
    {
        $status = true;
        $testimonial = new Testimonial($request->all());
        
        if (!policy($testimonial)->isCreatableBy(Auth::user(), $testimonial)) {
            $status = false;
            $error = 'You have no permissions to add testimonial';
            
            return new Response(compact('status', 'error'), 403);
        }
        
        $testimonial->user_id = Auth::user()->id;
        $testimonial->save();
        
        return new Response(compact('status', 'testimonial'), 200);
    }

}
