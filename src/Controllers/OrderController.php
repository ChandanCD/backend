<?php

require_once(APPROOT . 'interfaces/CsvOrderData.php');
require_once(APPROOT . 'Traits/ApiResponse.php');


use \interfaces\CsvOrderData;
use \Traits\ApiResponse;

/**
 *  load Mongolog logger framework
 *  
 */
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class OrderController implements CsvOrderData
{
    use ApiResponse;

    private $csvFilePath = '';
    private $logger;
    
    /**
     * __construct
     *
     * @param  mixed $filePath
     * @return void
     */
    public function __construct(string $csvFilePath)
    {
        $this->csvFilePath = $csvFilePath;

        //check if file
        $this->checkCsvFile($this->csvFilePath);
        /**
         * Create object of Logger with channel-name Info
         * Create object for StreamHandler to handle creating and writing logs into file
         */
        $this->logger = new Logger("info");
        $stream_handler = new StreamHandler(APPROOT . 'logs/app.log', Logger::DEBUG);
        $this->logger->pushHandler($stream_handler);
    }
     
    /**
     * processRequest
     * Process GET, POST method
     * @param  string $method
     * @param  string $action
     * @param  int $id
     * @return void
     */
    public function processRequest(string $method, ?string $action, ?int $id)
    {
        switch ([$method, $action]) {
            case ["GET", "getdata"]:
                return $this->successResponse(
                    $this->arrayToassociativeArray($this->readOrderData($this->csvFilePath)),
                     true, 200
                );
                break;
                
            case ["POST", "create"]:
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if ( ! empty($errors)) {
                    return $this->errorResponse($errors, false, 422);
                }

                return $this->addOrderData($data);
                break;

            case ["POST", "update"]:
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if ( ! empty($errors)) {
                    return $this->errorResponse(
                        $errors, false
                     , 422);
                }

                return $this->updateOrderData($data);

                break;
            case ["POST", "delete"]:

                return $this->deleteOrderData($id);

                break;

            case ["POST", "deleteMultipleOrders"]:
        
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $result = $this->deleteMultipleOrders($data);

                if($result){
                    return $this->successResponse(
                        "Order deleted",
                         true, 200
                    );
                }else{
                    return $this->errorResponse(
                        'Failed to delete', false
                     , 304);
                }

                break;
            
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }
 
  /**
   * readData
   * read data from csv
   * @param  string $path
   * @return array
   */
  public function readOrderData(string $path): array
  {
    try{
        $csv = [];
        // Open for reading only
        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $csv[] = $data;
            }
            fclose($handle);
        }else{
            throw new Exception("Failed to open file in write mode");
        }
        return $csv;
    }catch(Exception $e){

        $response = $e->getMessage();
        $this->logger->error($response);

        return $this->errorResponse(
            $response, false
         , 304);  
    }

  }
   
    /**
     * updateData
     * update data based on id
     * @param  array $data
     * @return int
     */
    public function updateOrderData(array $data)
    {
        try{

            $id = array_key_exists("id",$data) ? $data['id'] : 0 ;
            $getAllData = $this->readOrderData($this->csvFilePath);
            $num = count($getAllData);
            // skip header , start from next row
            for($i = 1; $i < $num; $i++){
                if (is_numeric($getAllData[$i][0]) && $getAllData[$i][0] == $id) {
                    $getAllData[$i] = array_values($data); // get only values from assiciative array
                    break;
                }
            }
    
            $result = $this->writeCSV($getAllData);

            if($result){

                return $this->successResponse(
                    "$id updated",
                     true, 200
                );
            }

        }catch(Exception $e){

            $response = $e->getMessage();
            $this->logger->error($response);
 
            return $this->errorResponse(
                 $response, false
              , 304);   
        }

    }
  
    /**
     * deleteData
     * remove data from csv
     * @param  int $id
     * @return bool
     */
    public function deleteOrderData(int $id)
    {
        try{
            
            if ($id == 0) throw new Exception("Order delete Id should not be 0.");

            $getAllData = $this->readOrderData($this->csvFilePath);
    
            $num = count($getAllData);
            // skip heaer , start from next row
            for($i = 1; $i < $num; $i++){
    
                if (is_numeric($getAllData[$i][0]) && $getAllData[$i][0] == $id) {
                    array_splice($getAllData, $i, 1); // splice current index from array
                }
    
            }
    
            $result = $this->writeCSV($getAllData);

            if($result){
                return $this->successResponse(
                    "data deleted",
                     true, 200
                );
            }

        }catch(Exception $e){

            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->errorResponse(
                $response, false
             , 304);   
        }
 
    }
 
    /**
     * addData
     * adds data into csv
     * @param  array $data
     * @return bool
     */
    public function addOrderData(array $data)
    {
        try{

            if(empty($data)) throw new Exception("Order can not be empty"); // check if empty array

            //get the count of records in csv file including header
            $getAllData = $this->readOrderData($this->csvFilePath);
            $num = count($getAllData);
    
            if(!array_key_exists("id", $data)){
                
               /* 
                total row with header row will be same as increamented
                preped the new key
               */
               $data = array("id" => $num) + $data;
            }else{
                $data["id"] = $num;
            }
            
            // push latest array to existing array
            array_push($getAllData, $data);
    
            $result = $this->writeCSV($getAllData);

            if($result){
                return $this->successResponse(
                    "data created",
                     true, 200
                );
            }

        }catch(Exception $e){

            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->errorResponse(
                $response, false
             , 304);    
        }

    }
            
    /**
     * writeCSV
     * Operns csv file in write mode 
     * writes array of data into csv file
     * @param  array $data
     * @return bool
     */
    public function writeCSV(array $data): bool
    {
        try { 

            if(count($data) == 0) throw new Exception("Length of array should not be 0.");

            // open file in write only mode
            if (($fhandle = fopen($this->csvFilePath, "w")) !== FALSE) {
                foreach ($data as $fields) {
                    fputcsv($fhandle, $fields);
                }
                return fclose($fhandle); // returns true or false
            }else{
                throw new Exception(" Failed to open file in write mode");
            } 

        }catch (Exception $e) {

            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->errorResponse(
                $response, false
             , 304);            
        }

    }
       
    /**
     * arrayToassociativeArray
     * combine header with associated data
     * creates new array with header as key
     * @param  array $data
     * @return array
     */
    public function arrayToassociativeArray(array $data): array
    {
        $header = array_shift($data); // get first row / header
        /*
        Iterate through array and combine 
        */
        $result = array_map(function ($line) use ($header) {
        $associativeArray = array_combine($header, $line); // combine both the array
        return $associativeArray;
        }, $data);
    
        return $result;
    }
    
    /**
     * deleteMultipleOrders
     *
     * @param  array $selectedOrders
     * @return void
     */
    public function deleteMultipleOrders(array $selectedOrders)
    {
        try{
            foreach($selectedOrders as $order){
                if (array_key_exists("id", $order)) {
                    $this->deleteOrderData($order["id"]);
                }
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
   
    /**
     * getValidationErrors
     * validate type of fields 
     * @param  array $data
     * @return array
     */
    public function getValidationErrors(array $data): array
    {
        $errors = []; // empty error array

        // destructering data array
        ['id' => $id, 'name' => $name, 'state' => $state, 'zip' => $zip,
         'amount' => $amount, 'quantity' => $quantity, 'item' => $item] = $data;

        if (!empty($data)) {

            if (array_key_exists("id", $data)) {

                if (filter_var($id, FILTER_VALIDATE_INT) === false) {
                    $errors['id'] = "id must be an integer";
                }
            }

            if (empty($name)) {
                $errors['name'] = "name is required";
            }else{
                $name_regex = "/^[a-zA-Z ]+$/i";
                if (!preg_match ($name_regex, $name) ) { 
                    $errors['name'] = "Name must be in Letters";
                }
            }

            if (empty($state)) {
                    $errors['state'] = "state is required";
            }else{
                $state_regex = "/^[a-zA-Z ]+$/i";
                if (!preg_match ($state_regex, $name) ) { 
                    $errors['state'] = "State must be in Letters";
                }
            }

            if (empty($zip)) {
                $errors['zip'] = "zip is required";
            } else {
                $zip_regex = "/^(?:\d{5,6})$/i"; 
                if (!preg_match($zip_regex, $zip)) {
                    $errors['zip'] = "ZipCode must be numbers and length must be 5 or 6 ";
                }
            }

            if (empty($amount)) {
                $errors['amount'] = "amount is required";
            } else {
                if (filter_var($amount, FILTER_VALIDATE_FLOAT) === false) {
                    $errors['amount'] = "Amount must be in decimal format e.g - 10.00";
                }
            }

            if (empty($quantity)) {
                $errors['quantity'] = "quantity is required";
            } else {
                if (filter_var($quantity, FILTER_VALIDATE_INT) === false) {
                    $errors['quantity'] = "Quantity must be in numbers";
                }
            }

            if (empty($item)) {
                $errors['item'] = "item is required";
            }else{
                $item_regex = "/^[a-zA-Z0-9]{3,10}$/";
                if (!preg_match ($item_regex, $item) ) { 
                    $errors['item'] = "Item must contain letters and numbers. Minimum length must be 2";
                }
            }
        }
        return $errors;
    }
    
    /**
     * checkCsvFile
     *
     * @param  string $csvFilePath
     * @return void
     */
    public function checkCsvFile(string $csvFilePath)
    {
        try {
            if(!file_exists($csvFilePath)){
                throw new Exception("File does not exists");
            }
        } catch (Exception $e) {
            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->errorResponse(
                $response, false
             , 304);
        }
    }

}







