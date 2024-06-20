<?php

namespace App\Traits;

trait ApiResponses{

    protected function ok($message,$statusCode = 200){
        return response()->json([
            'success' => true,
            'status' => $statusCode, 
            'message' => $message
        ],$statusCode);
    }

    Protected function success($message,$result,$statusCode = 200){
        return response()->json([
            'success' => true,
            'status' => $statusCode,
            'data' => $result,  
            'message' => $message
        ],$statusCode);
    }

    protected function error($message,$result,$statusCode = 402){
        return response()->json([
            'success' => false,
            'status' => $statusCode,
            'data' => $result,  
            'message' => $message
        ],$statusCode);
    }
}

