<?php


namespace App\Traits;


trait ApiResponser
{

    protected function success($message = null,$status = 200)
    {
        return response()->json(['success' => true, "message" => $message], $status);
    }

    protected function errors(array $message = [],$status = 403)
    {
        return response()->json(['success' => false, "errors " => $message], $status);
    }
    protected function failed($message = "",$status = 403)
    {
        return response()->json(['success' => false, "message" => $message], $status);
    }
    protected function unauthorized($message = null,$status = 401)
    {
        return response()->json(['success' => false, "message" => $message], $status);
    }
}
