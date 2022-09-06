<?php

class addDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testAddData()
    {
        $dataControllerbject = new DataController('tests/_data/data.csv');
        $data = ["id" => 5, "name" => 'Chandan', "state" => "Karnataka", 
        "zip" => 560009, "amount" => 25.05, "qty" => 8, "item" => '8AC123'];

        $result = $dataControllerbject->addData($data);
        
        $this->assertTrue($result, 'Failed to add data');

    }
}