<?php

class deleteDataTest extends \Codeception\Test\Unit
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
    public function testDeleteTest()
    {
        $dataControllerbject = new DataController('tests/_data/data.csv');

        //delete data
        $result = $dataControllerbject->deleteData(5);

        $this->assertTrue($result);
    }
}