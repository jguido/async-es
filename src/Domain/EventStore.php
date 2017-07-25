<?php


namespace Unrlab\Domain;


abstract class EventStore
{
    /**
     * Current version
     *
     * @var float
     */
    protected $version = 0;

    /**
     * List of events that are not committed to the EventStore
     *
     * @var Message[]
     */
    protected $recordedEvents = array();

    /**
     * @param Message[] $historyEvents
     * @return static
     */
    protected static function reconstituteFromHistory(array $historyEvents)
    {
        $instance = new static();
        $instance->replay($historyEvents);

        return $instance;
    }

    /**
     * We do not allow public access to __construct, this way we make sure that an aggregate root can only
     * be constructed by static factories
     */
    protected function __construct()
    {
    }

    /**
     * @return string representation of the unique identifier of the event store
     */
    abstract protected function getIdentifier();

    /**
     * @param int $index
     * @return Message
     */
    public function getFromHistory($index = 0)
    {
        return $this->recordedEvents[$index];
    }

    /**
     * Get pending events and reset stack
     *
     * @return Message[]
     */
    protected function popRecordedEvents()
    {
        $pendingEvents = $this->recordedEvents;

        $this->recordedEvents = array();

        return $pendingEvents;
    }

    /**
     * Record an aggregate changed event
     *
     * @param Message $event
     */
    protected function recordThat(Message $event)
    {
        $this->version += 1;

        $event->trackVersion($this->version);

        $this->recordedEvents[] = $event;

        $this->apply($event);
    }

    /**
     * Replay past events
     *
     * @param Message[] $historyEvents
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function replay(array $historyEvents)
    {
        foreach ($historyEvents as $pastEvent) {
            $this->version = $pastEvent->version();

            $this->apply($pastEvent);
        }
    }

    /**
     * Apply given event
     *
     * @param Message $e
     * @throws \RuntimeException
     */
    protected function apply(Message $e)
    {
        $handler = $this->determineEventHandlerMethodFor($e);
        $this->recordedEvents[] = $e;

        if (! method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                "Missing event handler method %s for event store %s",
                $handler,
                get_class($this)
            ));
        }

        $this->{$handler}($e);
    }

    /**
     * Determine event name
     *
     * @param Message $e
     *
     * @return string
     */
    protected function determineEventHandlerMethodFor(Message $e)
    {
        return 'when' . implode('', array_slice(explode('\\', get_class($e)), -1));
    }
}