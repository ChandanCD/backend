<?php

namespace Traits;

trait ApiResponse 
{
    
  /**
   * successResponse
   *
   * @param  mixed $data
   * @param  mixed $statue
   * @param  mixed $code
   * @return void
   */
  public function successResponse($data, $statue = true, $code)
  { 
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(['success' =>$statue,'data' => $data], $code);
  }

  
  /**
   * errorResponse
   *
   * @param  mixed $message
   * @param  mixed $statue
   * @param  mixed $code
   * @return void
   */
  public function errorResponse($message,$statue, $code)
  {
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(['success' =>$statue,'error' => $message, 'code' => $code], $code);
  }

}