<?php
define('APPROOT', __DIR__ . '/../../src/'); // APPROOT contains the root path of the project

require __DIR__ . "/../../src/Controllers/OrderController.php"; // include the class for the unit test cases

/**
 * readDataTest
 */
class readDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $csvFilePath = 'tests/_data/data.csv';
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests    
    /**
     * testReadData
     *
     * @return void
     */
    public function testReadData()
    {
        $dataCOntrollerbject = new OrderController($this->csvFilePath);

        //read data
        $result = $dataCOntrollerbject->readOrderData($this->csvFilePath);

        $this->assertIsArray($result, "returns array on success");
        $this->assertGreaterThanOrEqual(2, count($result)); // compare with number of array in csv file
    }
    
    /**
     * testFileReadable
     * Check the whether the given csv file is 
     * readable or not
     * @return void
     */
    public function testFileReadable(){
        $this->assertFileIsReadable($this->csvFilePath);
    }
    
    /**
     * testCsvWrite
     * Check the whether the given csv file is 
     * writable or not
     * @return void
     */
    public function testCsvWrite(): void
    {
        $this->assertFileIsWritable($this->csvFilePath);
    }
}