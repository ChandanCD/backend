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
     * testDeleteTest
     *
     * @return void
     */
    public function testDeleteTest()
    {

        $orderControllerObject = $this->createMock(OrderController::class);
        $orderControllerObject->method("deleteOrderData")->willReturn(true);

        $orderControllerObject
        ->expects($this->once())
        ->method("deleteOrderData")
        ->with(5);

        $result = $orderControllerObject->deleteOrderData(5);

        $this->assertTrue($result);
    }
   
    /**
     * testDeleteFail
     * If 0 is passed to deleteOrderData funtion it will 
     * Delete order data from csv file
     * @return void
     */
    public function testDeleteFail()
    {
        $orderControllerObject = $this->createMock(OrderController::class);
        $orderControllerObject->method("deleteOrderData")->willReturn(false);

        $orderControllerObject
        ->expects($this->once())
        ->method("deleteOrderData")
        ->with(0);

        $result = $orderControllerObject->deleteOrderData(0);

        $this->assertFalse($result);
    }
}