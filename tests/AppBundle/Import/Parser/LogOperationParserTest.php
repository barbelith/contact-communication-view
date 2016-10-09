<?php


namespace AppBundle\Import\Parser;


use AppBundle\Entity\Operation;
use AppBundle\Import\Mapping\LogOperationMapping;

class LogOperationParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider providerIsItemValid
     */
    public function isItemValid($item, $expected)
    {
        $parser = new LogOperationParser(new LogOperationMapping());

        $this->assertEquals($expected, $parser->isItemValid($item));
    }

    public function providerIsItemValid()
    {
        return [
            [null, false],
            ['', false],
            ['afaf', false],
            ['S7001112226112223331Movistar                02012016180130', true],
            ['C6112223336009998880Pepe                    01012016205203000142', true],
            ['U4815162342', true],
            ['S7001112226112223331Movistar                0201201618', false],
            ['C6112223336009998880Pepe                    01012016205203000', false],
            ['S611222333     14200                        05012016220000', true],
        ];
    }

    /**
     * @test
     */
    public function doesNotCreateOperationWithUnknownItem()
    {
        $log = 'U4815162342';
        $parser = new LogOperationParser(new LogOperationMapping());

        $operation = $parser->parseItem($log);

        $this->assertNull($operation);
    }

    /**
     * @test
     */
    public function createsOperation()
    {
        $log = 'C6112223336009998880Pepe                    01012016205203000142';
        $parser = new LogOperationParser(new LogOperationMapping());

        $operation = $parser->parseItem($log);

        $this->assertInstanceOf(ParsedOperation::class, $operation);
    }

    /**
     * @test
     */
    public function createsCallOperation()
    {
        $log = 'C6112223336009998880Pepe                    01012016205203000142';
        $parser = new LogOperationParser(new LogOperationMapping());

        $operation = $parser->parseItem($log);

        $this->assertEquals(Operation::TYPE_CALL, $operation->getType());
        $this->assertEquals('611222333', $operation->getPhoneOwner());
        $this->assertEquals('600999888', $operation->getContactNumber());
        $this->assertEquals(Operation::DIRECTION_OUTGOING, $operation->getDirection());
        $this->assertEquals('Pepe', $operation->getContactName());
        $this->assertEquals('2016-01-01T20:52:03+00:00', $operation->getDate()->format('c'));
        $this->assertEquals(142, $operation->getDuration());
    }

    /**
     * @test
     */
    public function createsIncomingCallOperation()
    {
        $log = 'C6009998886112223331Pepe                    01012016205203000142';
        $parser = new LogOperationParser(new LogOperationMapping());

        $operation = $parser->parseItem($log);

        $this->assertEquals(Operation::DIRECTION_INCOMING, $operation->getDirection());
        $this->assertEquals('600999888', $operation->getContactNumber());
    }

    /**
     * @test
     */
    public function setsCorrectNumberWithSpecialNumber()
    {
        $log = 'S611222333     14200                        05012016220000';
        $parser = new LogOperationParser(new LogOperationMapping());

        $operation = $parser->parseItem($log);

        $this->assertEquals('1420', $operation->getContactNumber());
    }

    /**
     * @test
     */
    public function setEmptyContactNameIfNotIncludedInLog()
    {
        $log = 'S611222333     14200                        05012016220000';
        $parser = new LogOperationParser(new LogOperationMapping());

        $operation = $parser->parseItem($log);

        $this->assertEquals(null, $operation->getContactName());
    }

    /**
     * @test
     */
    public function createsSMSOperation()
    {
        $log = 'S7001112226112223331Movistar                02012016180130';
        $parser = new LogOperationParser(new LogOperationMapping());

        $operation = $parser->parseItem($log);

        $this->assertEquals(Operation::TYPE_SMS, $operation->getType());
        $this->assertEquals('611222333', $operation->getPhoneOwner());
        $this->assertEquals('700111222', $operation->getContactNumber());
        $this->assertEquals(Operation::DIRECTION_INCOMING, $operation->getDirection());
        $this->assertEquals('Movistar', $operation->getContactName());
        $this->assertEquals('2016-01-02T18:01:30+00:00', $operation->getDate()->format('c'));
        $this->assertEquals(0, $operation->getDuration());
    }
}