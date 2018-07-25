<?php

namespace Otobank\Monolog\Processor;

use PHPUnit\Framework\TestCase;

class ConsoleProcessorTest extends TestCase
{
    /**
     * @dataProvider providerForInvoke
     */
    public function testInvoke($commandName, $expectedRecord)
    {
        $processor = new ConsoleProcessor();

        if ($commandName) {
            $commandp = $this->prophesize('\Symfony\Component\Console\Command\Command');
            $commandp->getName()->willReturn($commandName);
            $eventp = $this->prophesize('\Symfony\Component\Console\Event\ConsoleCommandEvent');
            $eventp->getCommand()->willReturn($commandp->reveal());

            $processor->onCommand($eventp->reveal());
        }

        $record = [];
        $record = $processor($record);

        $this->assertSame($expectedRecord, $record);
    }

    public function providerForInvoke()
    {
        return [
            [null,  []],
            ['foo', ['extra' => ['command_name' => 'foo']]],
        ];
    }
}
