<?php

namespace Otobank\Monolog\Processor;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class ConsoleProcessorTest extends TestCase
{
    /**
     * @dataProvider providerForInvoke
     */
    public function testInvoke($commandName, $expectedRecord)
    {
        $processor = new ConsoleProcessor();

        if ($commandName) {
            // Command
            $commandp = $this->prophesize(Command::class);
            $commandp->getName()->willReturn($commandName);

            // InputInterface
            $inputp = $this->prophesize(InputInterface::class);

            // OutputInterface
            $outputp = $this->prophesize(OutputInterface::class);

            // Event
            $eventp = $this->prophesize(ConsoleCommandEvent::class);
            $eventp->getCommand()->willReturn($commandp->reveal());
            $eventp->getInput()->willReturn($inputp->reveal());
            $eventp->getOutput()->willReturn($outputp->reveal());

            $processor->onCommand($eventp->reveal());
        }

        $record = [];
        $record = $processor($record);

        $this->assertEquals($expectedRecord, $record);
    }

    public function providerForInvoke()
    {
        return [
            [null,  []],
            ['foo', ['extra' => ['command_name' => 'foo']]],
        ];
    }

    /**
     * @dataProvider providerForInvokeWithClosure
     */
    public function testInvokeWithClosure($commandName, $expectedRecord)
    {
        $processor = new ConsoleProcessor(function ($command, $input, $output) {
            return [
                'command_hoge' => 'fuga',
                'input_bar' => $input->getArgument('bar'),
            ];
        });

        // Command
        $commandp = $this->prophesize(Command::class);
        $commandp->getName()->willReturn($commandName);

        // InputInterface
        $inputp = $this->prophesize(InputInterface::class);
        $inputp->getArgument('bar')->willReturn('baz');

        // OutputInterface
        $outputp = $this->prophesize(OutputInterface::class);

        // Event
        $eventp = $this->prophesize(ConsoleCommandEvent::class);
        $eventp->getCommand()->willReturn($commandp->reveal());
        $eventp->getInput()->willReturn($inputp->reveal());
        $eventp->getOutput()->willReturn($outputp->reveal());

        $processor->onCommand($eventp->reveal());

        $record = [];
        $record = $processor($record);

        $this->assertEquals($expectedRecord, $record);
    }

    public function providerForInvokeWithClosure()
    {
        return [
            ['foo', [
                'extra' => [
                    'input_bar' => 'baz',
                    'command_name' => 'foo',
                    'command_hoge' => 'fuga',
                ],
            ]],
        ];
    }
}
