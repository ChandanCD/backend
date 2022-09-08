<?php
require_once('interfaces/CsvUserData.php');
require_once('Traits/ApiResponse.php');

// namespace Traits;
use \interfaces\CsvUserData;
use \Traits\ApiResponse;


class DataController implements CsvUserData
{
    // use \Traits\ApiResponse;
    use ApiResponse;

    private $csvFile = '';
    public $test;
    
    /**
     * __construct
     *
     * @param  mixed $filePath
     * @return void
     */
    public function __construct(string $filePath)
    {
        $this->csvFile = $filePath;
    }
     
    /**
     * processRequest
     * Process GET, POST method
     * @param  mixed $method
     * @param  mixed $action
     * @param  mixed $id
     * @return void
     */
    public function processRequest(string $method, ?string $action, ?int $id)
    {
        switch ([$method, $action]) {
            case ["GET", "getdata"]:
                return $this->successResponse(
                    $this->arrayToassociativeArray($this->readData($this->csvFile)),
                     true, 200
                );
                break;
                
            case ["POST", "create"]:
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if ( ! empty($errors)) {
                    return $this->errorResponse($errors, false, 422);
                }

                $result = $this->addData($data);

                if($result){
                    return $this->successResponse(
                        "data created",
                         true, 200
                    );
                }else{
                    return $this->errorResponse(
                       'Failed to create', false
                    , 304);
                }
                
                break;

            case ["POST", "update"]:
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if ( ! empty($errors)) {
                    return $this->errorResponse(
                        $errors, false
                     , 422);
                }

                $result = $this->updateData($data);

                if($result){
                    return $this->successResponse(
                        "data updated",
                         true, 200
                    );
                }else{
                    return $this->errorResponse(
                        'Failed to update', false
                     , 304);
                }

                break;
            case ["POST", "delete"]:
                $result = $this->deleteData($id);

                if($result){
                    return $this->successResponse(
                        "data deleted",
                         true, 200
                    );
                }else{
                    return $this->errorResponse(
                        'Failed to detele', false
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
   * @param  mixed $path
   * @return array
   */
  public function readData(string $path): array
  {
    $csv = [];
    // Open for reading only
    if (($handle = fopen($path, "r")) !== FALSE) {
        while (($data = fgetcsv($handle)) !== FALSE) {
            $csv[] = $data;
        }
        fclose($handle);
    }
    return $csv;
  }
   
    /**
     * updateData
     * update data based on id
     * @param  mixed $data
     * @return int
     */
    public function updateData(array $data): int
    {
        $id = array_key_exists("id",$data) ? $data['id'] : 0 ;
        $getAllData = $this->readData($this->csvFile);
        $num = count($getAllData);
        // skip header , start from next row
        for($i = 1; $i < $num; $i++){
            if (is_numeric($getAllData[$i][0]) && $getAllData[$i][0] == $id) {
                $getAllData[$i] = array_values($data); // get only values from assiciative array
                break;
            }
        }

        $this->writeCSV($getAllData);
        return $id;
    }
  
    /**
     * deleteData
     * remove data from csv
     * @param  mixed $id
     * @return bool
     */
    public function deleteData(int $id): bool
    {
        if ($id == 0) return false;

        $getAllData = $this->readData($this->csvFile);

        $num = count($getAllData);
        // skip heaer , start from next row
        for($i = 1; $i < $num; $i++){

            if (is_numeric($getAllData[$i][0]) && $getAllData[$i][0] == $id) {
                $getAllData[$i] = ''; // set array value to empty
            }

        }

        return $this->writeCSV($getAllData);
    }
 
    /**
     * addData
     * adds data into csv
     * @param  mixed $data
     * @return bool
     */
    public function addData(array $data): bool
    {
        if(empty($data)) return false; // check if empty array

        //get the count of records in csv file including header
        $getAllData = $this->readData($this->csvFile);
        $num = count($getAllData);
        // check if file exists or not
        if(!file_exists($this->csvFile)){
            $keys = ["id","name","state","zip","amount","qty","item"];
            $fp = fopen($this->csvFile, 'a'); //Open in append mode to write at the end
            fputcsv($fp, $keys); // set header
        }else{
            $fp = fopen($this->csvFile, 'a'); //Open in append mode to write at the end
        }

        if(!array_key_exists("id", $data)){
            
           /* 
            total row with header row will be same as increamented
            preped the new key
           */
           $data = array("id" => $num) + $data;
        }else{
            $data["id"] = $num;
        }
        
        fputcsv($fp, $data);
        return fclose($fp);
    }
            
    /**
     * writeCSV
     * Operns csv file in write mode 
     * writes array of data into csv file
     * @param  mixed $data
     * @return bool
     */
    public function writeCSV(array $data): bool
    {  
        if(count($data) == 0) return false;

        // open file in write only mode
        if (($fhandle = fopen($this->csvFile, "w")) !== FALSE) {
            foreach ($data as $fields) {
                fputcsv($fhandle, $fields);
            }
            return fclose($fhandle); // returns true or false
        }else{
            return false;
        } 
    }
       
    /**
     * arrayToassociativeArray
     * combine header with associated data
     * creates new array with header as key
     * @param  mixed $data
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
     * getValidationErrors
     * validate type of fields 
     * @param  mixed $data
     * @return array
     */
    public function getValidationErrors(array $data): array
    {
        $errors = []; // empty error array

        // destructering data array
        ['id' => $id, 'name' => $name, 'state' => $state, 'zip' => $zip,
         'amount' => $amount, 'qty' => $qty, 'item' => $item] = $data;

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
                    $errors['name'] = "Name is not valid";
                }
            }

            if (empty($state)) {
                    $errors['state'] = "state is required";
            }else{
                $state_regex = "/^[a-zA-Z ]+$/i";
                if (!preg_match ($state_regex, $name) ) { 
                    $errors['state'] = "State is not valid";
                }
            }

            if (empty($zip)) {
                $errors['zip'] = "zip is required";
            } else {
                $zip_regex = "/^(?:\d{5,6})$/i"; 
                if (!preg_match($zip_regex, $zip)) {
                    $errors['zip'] = "ZipCode is not valid";
                }
            }

            if (empty($amount)) {
                $errors['amount'] = "amount is required";
            } else {
                if (filter_var($amount, FILTER_VALIDATE_FLOAT) === false) {
                    $errors['amount'] = "Amount is not valid";
                }
            }

            if (empty($qty)) {
                $errors['qty'] = "qty is required";
            } else {
                if (filter_var($qty, FILTER_VALIDATE_INT) === false) {
                    $errors['qty'] = "Qty is not valid";
                }
            }

            if (empty($item)) {
                $errors['item'] = "item is required";
            }else{
                $item_regex = "/^[a-zA-Z0-9]{3,10}$/";
                if (!preg_match ($item_regex, $item) ) { 
                    $errors['item'] = "Item is not valid";
                }
            }
        }
        return $errors;
    }


}







