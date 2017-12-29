<?php

namespace marvin255\bxmailer\tests;

use marvin255\bxmailer\Mailer;

class MailerTest extends BaseTestCase
{
    public function testSetHandler()
    {
        $mailer = Mailer::getInstance(true);

        $handler = $this->getMockBuilder('\marvin255\bxmailer\HandlerInterface')
            ->getMock();

        $this->assertSame(
            null,
            $mailer->getHandler()
        );
        $this->assertSame(
            $mailer,
            $mailer->setHandler($handler)
        );
    }

    public function testSend()
    {
        $message = $this->getMockBuilder('\marvin255\bxmailer\MessageInterface')
            ->getMock();

        $handler = $this->getMockBuilder('\marvin255\bxmailer\HandlerInterface')
            ->getMock();
        $handler->expects($this->once())
            ->method('send')
            ->with($this->equalTo($message))
            ->will($this->returnValue(true));

        $mailer = Mailer::getInstance(true);
        $mailer->setHandler($handler);

        $this->assertSame(
            true,
            $mailer->send($message)
        );
    }

    public function testSendException()
    {
        $message = $this->getMockBuilder('\marvin255\bxmailer\MessageInterface')
            ->getMock();

        $exception = 'exception_' . mt_rand();
        $handler = $this->getMockBuilder('\marvin255\bxmailer\HandlerInterface')
            ->getMock();
        $handler->expects($this->once())
            ->method('send')
            ->with($this->equalTo($message))
            ->will($this->throwException(new \Exception($exception)));

        $mailer = Mailer::getInstance(true);
        $mailer->setHandler($handler);

        $this->assertSame(
            false,
            $mailer->send($message)
        );
        $this->assertSame(
            $exception,
            $mailer->getLastError()
        );
        $this->assertContains(
            $exception,
            \CEventLog::$add['DESCRIPTION']
        );
    }
}
