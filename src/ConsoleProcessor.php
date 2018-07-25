<?php

namespace Otobank\Monolog\Processor;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Monolog Processor for console.
 */
class ConsoleProcessor implements EventSubscriberInterface
{
    protected $command;
    protected $input;
    protected $output;
    protected $closure;

    public function __construct(callable $closure = null)
    {
        $this->closure = $closure;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        $extra = [];

        if ($this->command) {
            $name = $this->command->getName();
            $extra['command_name'] = $name;
        }

        if ($this->closure) {
            $other = call_user_func($this->closure, $this->command, $this->input, $this->output);
            if (!is_array($other)) {
                throw new \DomainException('Must return array');
            }

            $extra = array_merge($extra, $other);
        }

        if (empty($extra)) {
            return $record;
        }

        if (isset($record['extra'])) {
            $record['extra'] = array_merge($record['extra'], $extra);
        } else {
            $record['extra'] = $extra;
        }

        return $record;
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onCommand(ConsoleCommandEvent $event)
    {
        $this->command = $event->getCommand();
        $this->input = $event->getInput();
        $this->output = $event->getOutput();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'onCommand',
        ];
    }
}
