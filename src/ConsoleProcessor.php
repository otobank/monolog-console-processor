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

    /**
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        if ($this->command) {
            $name = $this->command->getName();

            $record['extra']['command_name'] = $name;
        }

        return $record;
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onCommand(ConsoleCommandEvent $event)
    {
        $this->command = $event->getCommand();
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
