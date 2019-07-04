<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function __construct() {
        /**
         * Add following headers because of ajax requests goes from
         * another development server. Remove after React builder bundle
         * integrated to admin panel
         */
        //header('Access-Control-Allow-Credentials: true');
        //header('Access-Control-Allow-Origin: http://192.168.1.219:8888');
    }
    
    public function upload(Request $request)
    {
        // Current option icon
        $currentIcon = $request->input('currentIcon');
        
        return json_encode([
            'success' => true,
            'optionId' => intval($request->input('optionId')),
            'currentIcon' => $currentIcon,
            'path' => $request->file('icon')->store('icons')
        ]);
    }
}
