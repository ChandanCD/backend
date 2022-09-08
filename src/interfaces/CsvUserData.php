<?php

namespace Interfaces;

interface CsvUserData 
{

    public function processRequest(string $method, ?string $action, ?int $id);
    public function readData(string $path);
    public function updateData(array $data);
    public function deleteData(int $id);
    public function addData(array $data);
    public function writeCSV(array $data);
}
