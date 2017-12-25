<?php

namespace marvin255\bxmailer\tests;

use marvin255\bxmailer\Mailer;

class MailerTest extends BaseTestCase
{
    public function testSetOptions()
    {
        $mailer = Mailer::getInstance(true);
        $options = [
            'key_1_' . mt_rand() => 'value_1_' . mt_rand(),
            'key_2_' . mt_rand() => 'value_2_' . mt_rand(),
        ];

        $this->assertSame(
            [],
            $mailer->getOptions()
        );
        $this->assertSame(
            $mailer,
            $mailer->setOptions($options)
        );
        $this->assertSame(
            $options,
            $mailer->getOptions()
        );
    }

    public function testSetHandler()
    {
        $mailer = Mailer::getInstance(true);
        $options = [
            'key1' => 'value_1_' . mt_rand(),
            'key2' => 'value_2_' . mt_rand(),
        ];
        $mailer->setOptions($options);

        $handler = $this->getMockBuilder('\marvin255\bxmailer\HandlerInterface')
            ->setMethods(['setKey1', 'send'])
            ->getMock();
        $handler->expects($this->once())
            ->method('setKey1')
            ->with($this->equalTo($options['key1']));
        $handler->key2 = 'default_key_2_' . mt_rand();

        $this->assertSame(
            null,
            $mailer->getHandler()
        );
        $this->assertSame(
            $mailer,
            $mailer->setHandler($handler)
        );
        $this->assertSame(
            $options['key2'],
            $handler->key2
        );
        $this->assertSame(
            $handler,
            $mailer->getHandler()
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
        $this->assertContains(
            $exception,
            \CEventLog::$add['DESCRIPTION']
        );
    }
}
