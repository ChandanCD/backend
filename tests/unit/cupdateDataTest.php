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
     * updateOrderData takes array as a parameter
     * with fields mentioned as following
     * @return void
     */
    public function testUpdateData()
    {
        
        $orderControllerObject = $this->createMock(OrderController::class);

        $orderControllerObject
        ->expects($this->once())
        ->method("updateOrderData")
        ->with(["id" => 5, "name" => 'Chandan', "state" => "Karnataka", 
        "zip" => 560009, "amount" => 25.05, "qty" => 8, "item" => '8AC123'])
        ->will($this->returnValue(true));


        $result = $orderControllerObject->updateOrderData(["id" => 5, "name" => 'Chandan', "state" => "Karnataka", 
        "zip" => 560009, "amount" => 25.05, "qty" => 8, "item" => '8AC123']);

        $this->assertTrue($result);
    }
    
    /**
     * testUpdateDataFail
     * If Id is not passed in array to updateOrderData
     * it will not update into the csv file
     * @return void
     */
    public function testUpdateDataFail()
    {
        $orderControllerObject = $this->createMock(OrderController::class);

        $orderControllerObject
        ->expects($this->once())
        ->method("updateOrderData")
        ->with(["name" => 'Chandan', "state" => "Karnataka", 
        "zip" => 560009, "amount" => 25.05, "qty" => 8, "item" => '8AC123'])
        ->will($this->returnValue(false));


        $result = $orderControllerObject->updateOrderData(["name" => 'Chandan', "state" => "Karnataka","zip" => 560009, "amount" => 25.05, "qty" => 8, "item" => '8AC123']);

        $this->assertFalse($result);
    }
}