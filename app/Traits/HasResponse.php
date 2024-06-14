<?php

namespace App\Traits;


trait HasResponse
{
    /**
     * Format response
     * @param string status_code
     * @param string message
     * @param array data 
     * 
     */
    protected function sendResponse($status_code, $data, $options=null) {
        if($options)
            return response()->json($data, $status_code,[],$options);
        else
            return response()->json($data, $status_code);
    }
}
