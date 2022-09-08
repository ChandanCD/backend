<?php

/**
 * deleteDataTest
 */
class deleteDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
        
    /**
     * _before
     *
     * @return void
     */
    protected function _before()
    {
    }
    
    /**
     * _after
     *
     * @return void
     */
    protected function _after()
    {
    }
   
    /**
     * testDeleteFail
     * should return false on faile
     * @return void
     */
    public function testDeleteFail()
    {
        $dataControllerbject = new DataController('tests/_data/data.csv');
        //delete data
        $result = $dataControllerbject->deleteData(0);  
        $this->assertFalse($result);
    }
}