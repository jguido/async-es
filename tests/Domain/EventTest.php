<?php


namespace Tests\Domain;


use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Unrlab\Domain\Message;

class EventTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_a_new_uuid_after_construct()
    {
        $event = Message::occur('1', array());

        self::assertInstanceOf(Uuid::class, $event->uuid());
    }

    /**
     * @test
     */
    public function it_reference_an_event()
    {
        $event = Message::occur('1', array());

        self::assertEquals(1, $event->getId());
    }

    /**
     * @test
     */
    public function it_has_an_occurred_on_datetime_after_construct()
    {
        $event = Message::occur('1', array());

        $this->assertInstanceOf('\DateTimeImmutable', $event->createdAt());
    }

    /**
     * @test
     */
    public function it_has_assigned_payload_after_construct()
    {
        $payload = array('test payload');

        $event = Message::occur('1', $payload);

        $this->assertEquals($payload, $event->payload());
    }

    /**
     * @test
     */
    public function it_can_track_message_version()
    {
        $event = Message::occur('1', array('key' => 'value'));

        $event->trackVersion(2);

        $this->assertEquals(2, $event->version());
    }

    /**
     * @test
     */
    public function it_only_tracks_version_once()
    {
        $event = Message::occur('1', array('key' => 'value'));

        $event->trackVersion(2);

        $this->expectException('\BadMethodCallException');

        $event->trackVersion(3);
    }
}