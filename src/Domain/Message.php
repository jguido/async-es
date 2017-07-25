<?php


namespace Unrlab\Domain;


use Assert\Assertion;
use Prooph\Common\Messaging\DomainEvent;

class Message extends DomainEvent
{
    /**
     * @var array
     */
    protected $payload;
    /**
     * @var int
     */
    private $version;

    /**
     * Message constructor.
     * @param array $payload
     */
    public function __construct($id, array $payload, $class)
    {
        $this->init();
        $this->setPayload($payload);
        $this->metadata['event_id'] = $id;
    }


    /**
     * @param string $id
     * @param array $payload
     * @return static
     */
    public static function occur($id, array $payload)
    {
        $instance = new static($id, $payload, get_called_class());

        //We reset version here, because the AggregateTranslator will inject the version of the aggregate via method trackVersion
        $instance->version = null;

        return $instance;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->metadata['event_id'];
    }

    /**
     * Track version of related aggregate
     *
     * @param int $version
     * @throws \BadMethodCallException If event already tracks a version
     */
    public function trackVersion($version)
    {
        if (! is_null($this->version)) {
            throw new \BadMethodCallException(sprintf(
                "DomainEvent %s (%s) already tracks a version",
                get_class($this),
                $this->uuid->toString()
            ));
        }

        $this->setVersion($version);
    }

    /**
     * @param int $version
     */
    protected function setVersion($version)
    {
        Assertion::integer($version);

        $this->version = $version;
    }

    /**
     * @param string $id
     */
    protected function setId($id)
    {
        Assertion::string($id);
        Assertion::notEmpty($id);

        $this->metadata['event_id'] = $id;
    }

    protected function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @return int
     */
    public function version(): int
    {
        return$this->version;
    }
}