<?php

class arrayToassociativeArrayTest extends \Codeception\Test\Unit
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
     * testassociativeArray
     * make first array elements as key to rest of the
     * array element
     * @return void
     */
    public function testassociativeArray()
    {
        $dataControllerbject = new DataController(__DIR__ . '/src/data.csv');
        $data = [
            ["id", "name", "state","zip", "amount", "qty", "item"],
            [1, 'Chandan', "Karnataka", 560009, 25.05, 8, '8AC123']
        ];

        $result = $dataControllerbject->arrayToassociativeArray($data);
        
        $this->assertArrayHasKey("name", $result[0]);
    }
}