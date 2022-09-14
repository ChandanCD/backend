<?php

namespace Interfaces;

interface CsvOrderData
{
    
    /**
     * processRequest based on GET, POST and actions
     * this function acts as a route helps in directing to 
     * controller function based on actions (getdata, create ,update , delete etc.)
     *
     * @param  string $method
     * @param  string $action
     * @param  int $id
     * @return void
     */
    public function processRequest(string $method, ?string $action, ?int $id);    
    /**
     * readOrderData
     * opens csv file in read mode
     * reads all the rows of csv and push into an array ,
     * then returns the array
     * @param  string $csvFilePath
     * @return void
     */
    public function readOrderData(string $csvFilePath);    
    /**
     * updateOrderData
     * read order data from csv file , compare row-id with given id and
     * re-assign new array to selected row
     * @param  array $data
     * @return void
     */
    public function updateOrderData(array $data);    
    /**
     * deleteOrderData
     * read order data from csv file , compare row-id with given id and
     * splice/remove the array from existing order data
     * @param  int $id
     * @return void
     */
    public function deleteOrderData(int $id);    
    /**
     * addOrderData
     * read order data from csv file , compare row-id with given id and
     * push new array data exiting array and write into csv file
     * @param  array $data
     * @return void
     */
    public function addOrderData(array $data); 
        
    /**
     * arrayToassociativeArray
     * combine header with associated data
     * creates new array with header as key
     * @param  array $data
     * @return void
     */
    public function arrayToassociativeArray(array $data);
    /**
     * deleteMultipleOrders
     *
     * @param  array $selectedOrders
     * @return void
     */
    public function deleteMultipleOrders(array $selectedOrders);    
    /**
     * writeCSV
     * Operns csv file in write mode 
     * writes array of data into csv file
     * @param  array $data
     * @return void
     */
    public function writeCSV(array $data);
    
    /**
     * getValidationErrors
     * checks if any field is mandatory or not
     * check allowed characters and numbers for indivisual fields
     * @param  array $data
     * @return void
     */
    public function getValidationErrors(array $data);
}
