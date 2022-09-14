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

      
    /**
     * testAddData
     * This function will create mock of the OrderCOntroller
     * Here declaring the method addOrderData and it will return 
     * bool(true)
     * 
     * @return void
     */
    public function testAddData()
    {
        $orderControllerObject = $this->createMock(OrderController::class);
        $orderControllerObject->method("addOrderData")->willReturn(true);

        $orderControllerObject
        ->expects($this->once())
        ->method("addOrderData")
        ->with(["id" => 5, "name" => 'Chandan', "state" => "Karnataka", 
        "zip" => 560009, "amount" => 25.05, "qty" => 8, "item" => '8AC123']);

        $result = $orderControllerObject->addOrderData(["id" => 5, "name" => 'Chandan', "state" => "Karnataka", 
        "zip" => 560009, "amount" => 25.05, "qty" => 8, "item" => '8AC123']);

        $this->assertTrue($result);
    }
    
    /**
     * testAddDataFail
     * If we send empty array to addOrderData
     * it will not add the record to csv file
     * @return void
     */
    public function testAddDataFail()
    {
        $orderControllerObject = $this->createMock(OrderController::class);
        $orderControllerObject->method("addOrderData")->willReturn(true);

        $orderControllerObject
        ->expects($this->once())
        ->method("addOrderData")
        ->with([]);

        $result = $orderControllerObject->addOrderData([]);

        $this->assertTrue($result);

    }
}