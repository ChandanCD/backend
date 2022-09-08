<?php

class updateDataTest extends \Codeception\Test\Unit
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
  
    /**
     * testUpdateData
     *
     * @return void
     */
    public function testUpdateData()
    {
        $dataControllerbject = new DataController('tests/_data/data.csv');
        $data = [
            "id" => 1, "name" => 'Chandan', "state" => "Karnataka", 
            "zip" => 560009, "amount" => 25.05, "qty" => 8, "item" => '8AC123'
        ];
        //update data
        $result = $dataControllerbject->updateData($data);

        $this->assertIsInt($result);
    }
    
    /**
     * testUpdateDataFail
     *
     * @return void
     */
    public function testUpdateDataFail()
    {
        $dataControllerbject = new DataController('tests/_data/data.csv');
        $data = [
            "name" => 'Chandan', "state" => "Karnataka", 
            "zip" => 560009, "amount" => 25.05, "qty" => 8, "item" => '8AC123'
        ];
        //update data
        $result = $dataControllerbject->updateData($data);
        $this->assertSame(0, $result);
    }
}