<?php

class DataController
{
    private $csvFile = '';

    public function __construct(string $filePath)
    {
        $this->csvFile = $filePath;
    }
    
    /*
    Process GET, POST method
    Method parameter is Mandatory
    action and id is optional
    */
    public function processRequest(string $method, ?string $action, ?string $id)
    {
        switch ([$method, $action]) {
            case ["GET", "getdata"]:
                echo json_encode($this->arrayToassociativeArray($this->readData($this->csvFile)));
                break;
                
            case ["POST", "create"]:
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if ( ! empty($errors)) {
                    $this->sendOutput([
                        "error" => $errors,
                    ], 422);
                    break;
                }

                $result = $this->addData($data);

                if($result){
                    $this->sendOutput([
                        "message" => "data created",
                    ], 200);
                }else{
                    $this->sendOutput([
                        "error" => 'Failed to create',
                    ], 304);
                }
                
                break;

            case ["POST", "update"]:
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if ( ! empty($errors)) {
                    $this->sendOutput([
                        "error" => $errors,
                    ], 422);
                    break;
                }

                $result = $this->updateData($data);

                if($result){
                    $this->sendOutput([
                        "message" => "data updated",
                        "id" => $id
                    ], 200);
                }else{
                    $this->sendOutput([
                        "error" => 'Failed to update',
                        "id" => $id
                    ], 304);
                }

                break;
            case ["POST", "delete"]:
                $result = $this->deleteData($id);

                if($result){
                    $this->sendOutput([
                        "message" => "data deleted",
                        "id" => $id
                    ], 200);
                }else{
                    $this->sendOutput([
                        "error" => 'Failed to detele',
                        "id" => $id
                    ], 200);
                }

                break;
            
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }
  /*
  read data from csv and return as array
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
    /*
    update data based on id
    */
    public function updateData(array $data): int
    {
        $id = $data['id'];
        $getAllData = $this->readData($this->csvFile);
        $this->num = count($getAllData);
        // skip header , start from next row
        for($i = 1; $i < $this->num; $i++){
            if (is_numeric($getAllData[$i][0]) && $getAllData[$i][0] == $id) {
                $getAllData[$i] = array_values($data); // get only values from assiciative array
                break;
            }
        }

        $this->writeCSV($getAllData);
        return $id;
    }
    /*
    remove data from csv based on id
    */
    public function deleteData(string $id): bool
    {
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

    /*
    creates new file with defined header if not exists and appends data
    append new data into csv
    */
    public function addData(array $data): bool
    {
        // check if file exists or not
        if(!file_exists($this->csvFile)){
            $keys = ["id","name","state","zip","amount","qty","item"];
            $fp = fopen($this->csvFile, 'a'); //Open in append mode to write at the end
            fputcsv($fp, $keys); // set header
        }else{
            $fp = fopen($this->csvFile, 'a'); //Open in append mode to write at the end
        }
        fputcsv($fp, $data);
        return fclose($fp);
    }
    /*
    Operns csv file in srite mode 
    writes array of data into csv file
    */
    public function writeCSV(array $data): bool
    {  
        // open file in write only mode
        if (($fhandle = fopen($this->csvFile, "w")) !== FALSE) {
            foreach ($data as $fields) {
                fputcsv($fhandle, $fields);
            }
            $result = fclose($fhandle);

            return $result; // returns true or false
        }
        return false;
    }

    /*
     combine header with associated data
     creates new array with header as key
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

    /*
    format data in json and send to end point with http status code
    */
    protected function sendOutput(array $data, int $status_code): void
    {   
        http_response_code($status_code);
        echo json_encode($data);
    }

    /*
    validate type of fields 
    */
    public function getValidationErrors(array $data): array
    {
        $errors = [];

        if (!empty($data)) {

            if (array_key_exists("id", $data)) {

                if (filter_var($data["id"], FILTER_VALIDATE_INT) === false) {
                    $errors[] = "id must be an integer";
                }
            }

            if (empty($data["name"])) {
                $errors[] = "name is required";
            }

            if (empty($data["state"])) {
                if (filter_var($data["id"], FILTER_VALIDATE_INT) === false) {
                    $errors[] = "state is required";
                }
            }

            if (empty($data["zip"])) {
                $errors[] = "zip is required";
            } else {
                if (filter_var($data["zip"], FILTER_VALIDATE_INT) === false) {
                    $errors[] = "zip must be an integer";
                }
            }

            if (empty($data["amount"])) {
                $errors[] = "amount is required";
            } else {
                if (filter_var($data["amount"], FILTER_VALIDATE_FLOAT) === false) {
                    $errors[] = "amount must be an float";
                }
            }

            if (empty($data["qty"])) {
                $errors[] = "qty is required";
            } else {
                if (filter_var($data["qty"], FILTER_VALIDATE_INT) === false) {
                    $errors[] = "qty must be an integer";
                }
            }

            if (empty($data["item"])) {
                $errors[] = "item is required";
            }
        }
        print_r($errors);
        return $errors;
    }


}







