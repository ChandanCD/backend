<?php

class readDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $csvFile = __DIR__ . "/../_data/data.csv";
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testReadData()
    {
        $dataCOntrollerbject = new DataController('tests/_data/data.csv');

        //read data
        $result = $dataCOntrollerbject->readData($this->csvFile);

        $this->assertIsArray($result, "returns array on success");
        $this->assertGreaterThanOrEqual(2, count($result)); // compare with number of array in csv file
    }
}