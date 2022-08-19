<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Message;
use App\Entity\Signal;
use PHPUnit\Framework\TestCase;

class SignalTest extends TestCase
{
    private function getSignalAndMessage(): array
    {
        $msg = new Message();
        $signal = new Signal();

        $signal->setMessage($msg);
        $msg->addSignal($signal);

        return [$signal, $msg];
    }

    public function testIncrementMessageNbSignals(): void
    {
        /**
         * @var Signal $signal
         * @var Message $msg
        */
        [$signal, $msg] = $this->getSignalAndMessage();

        $this->assertSame(0, $msg->getNbSignals());

        $signal->incrementMessageNbSignals();
        $this->assertSame(1, $msg->getNbSignals());

        $signal->incrementMessageNbSignals();
        $this->assertSame(2, $msg->getNbSignals());
    }

    public function testDecrementMessageNbSignals(): void
    {
        /**
         * @var Signal $signal
         * @var Message $msg
        */
        [$signal, $msg] = $this->getSignalAndMessage();

        $msg->setNbSignals(2);

        $signal->decrementMessageNbSignals();
        $this->assertSame(1, $msg->getNbSignals());

        $signal->decrementMessageNbSignals();
        $this->assertSame(0, $msg->getNbSignals());

        $signal->decrementMessageNbSignals();
        $this->assertSame(0, $msg->getNbSignals());
    }

    public function testIncrementMessageNbSignalsByType(): void
    {
        /**
         * @var Signal $signal
         * @var Message $msg
        */
        [$signal, $msg] = $this->getSignalAndMessage();

        $signal->setMotif(Signal::HAINE);
        $signal->incrementMessageNbSignalsByType();

        $this->assertSame([Signal::HAINE => 1], $msg->getNbSignalsByType());

        $signal->setMotif(Signal::HAINE);
        $signal->incrementMessageNbSignalsByType();

        $this->assertSame([Signal::HAINE => 2], $msg->getNbSignalsByType());

        $signal->setMotif(Signal::SEX);
        $signal->incrementMessageNbSignalsByType();

        $this->assertSame([Signal::HAINE => 2, Signal::SEX => 1], $msg->getNbSignalsByType());
    }

    public function testDecrementMessageNbSignalsByType(): void
    {
        /**
         * @var Signal $signal
         * @var Message $msg
        */
        [$signal, $msg] = $this->getSignalAndMessage();

        $msg->setNbSignalsByType([Signal::HAINE => 2, Signal::SEX => 1]);

        $signal->setMotif(Signal::HAINE);
        $signal->decrementMessageNbSignalsByType();

        $this->assertSame([Signal::HAINE => 1, Signal::SEX => 1], $msg->getNbSignalsByType());

        $signal->setMotif(Signal::HAINE);
        $signal->decrementMessageNbSignalsByType();

        $this->assertSame([Signal::SEX => 1], $msg->getNbSignalsByType());
    }
}
