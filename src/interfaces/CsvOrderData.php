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
     *
     * @param  string $path
     * @return void
     */
    public function readOrderData(string $path);    
    /**
     * updateOrderData
     *
     * @param  array $data
     * @return void
     */
    public function updateOrderData(array $data);    
    /**
     * deleteOrderData
     *
     * @param  int $id
     * @return void
     */
    public function deleteOrderData(int $id);    
    /**
     * addOrderData
     *
     * @param  array $data
     * @return void
     */
    public function addOrderData(array $data);    
    /**
     * deleteMultipleOrders
     *
     * @param  array $selectedOrders
     * @return void
     */
    public function deleteMultipleOrders(array $selectedOrders);    
    /**
     * writeCSV
     *
     * @param  array $data
     * @return void
     */
    public function writeCSV(array $data);
}
