<?php

/**
 * readDataTest
 */
class readDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $csvFile = 'tests/_data/data.csv';
    
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
        $dataCOntrollerbject = new DataController($this->csvFile);

        //read data
        $result = $dataCOntrollerbject->readData($this->csvFile);

        $this->assertIsArray($result, "returns array on success");
        $this->assertGreaterThanOrEqual(2, count($result)); // compare with number of array in csv file
    }
    
    /**
     * testFileReadable
     * check if file is radable or not
     * @return void
     */
    public function testFileReadable(){
        $this->assertFileIsReadable($this->csvFile);
    }
    
    /**
     * testCsvWrite
     * check if file is writable or not
     * @return void
     */
    public function testCsvWrite(): void
    {
        $this->assertFileIsWritable($this->csvFile);
    }
}