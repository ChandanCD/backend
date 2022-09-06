<?php

class DataController
{
    private $csvFile = '';

    public function __construct(string $filePath)
    {
        $this->csvFile = $filePath;
    }
    
    public function processRequest(string $method, ?string $action, ?string $id)
    {
        switch ([$method, $action]) {
            case ["GET", "getdata"]:
                echo json_encode($this->arrayToassociativeArray($this->readData($this->csvFile)));
                break;
                
            case ["POST", "create"]:
                $data = (array) json_decode(file_get_contents("php://input"), true);

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

    protected function sendOutput(array $data, int $status_code): void
    {   
        http_response_code($status_code);
        echo json_encode($data);
    }


}







