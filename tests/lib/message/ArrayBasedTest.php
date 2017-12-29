<?php

namespace marvin255\ArrayBaseder\tests\message;

use marvin255\bxmailer\tests\BaseTestCase;
use marvin255\bxmailer\message\ArrayBased;

class ArrayBasedTest extends BaseTestCase
{
    public function testGetTo()
    {
        $emails = ['email_1_' . mt_rand(), 'email_2_' . mt_rand()];
        $message = new ArrayBased(['to' => $emails]);

        $this->assertEquals(
            $emails,
            $message->getTo()
        );
    }

    public function testGetCc()
    {
        $emails = ['email_1_' . mt_rand(), 'email_2_' . mt_rand()];
        $message = new ArrayBased(['cc' => $emails]);

        $this->assertEquals(
            $emails,
            $message->getCc()
        );
    }

    public function testGetBcc()
    {
        $emails = ['email_1_' . mt_rand(), 'email_2_' . mt_rand()];
        $message = new ArrayBased(['bcc' => $emails]);

        $this->assertEquals(
            $emails,
            $message->getBcc()
        );
    }

    public function testGetFrom()
    {
        $from = 'from_' . mt_rand();
        $message = new ArrayBased(['from' => $from]);

        $this->assertEquals(
            $from,
            $message->getFrom()
        );
    }

    public function testGetReplyTo()
    {
        $replyTo = 'reply_' . mt_rand();
        $message = new ArrayBased(['replyTo' => $replyTo]);

        $this->assertEquals(
            $replyTo,
            $message->getReplyTo()
        );
    }

    public function testGetSubject()
    {
        $subject = 'subject_' . mt_rand();
        $message = new ArrayBased(['subject' => $subject]);

        $this->assertEquals(
            $subject,
            $message->getSubject()
        );
    }

    public function testGetMessage()
    {
        $body = 'message_' . mt_rand();
        $message = new ArrayBased(['message' => $body]);

        $this->assertEquals(
            $body,
            $message->getMessage()
        );
    }

    public function testGetIsHtml()
    {
        $message = new ArrayBased(['isHtml' => true]);
        $message2 = new ArrayBased(['isHtml' => null]);

        $this->assertEquals(
            true,
            $message->isHtml()
        );
        $this->assertEquals(
            false,
            $message2->isHtml()
        );
    }

    public function testGetAdditionalHeaders()
    {
        $addHeaders = [
            'header_1' => 'value_1_' . mt_rand(),
            'header_2' => 'value_2_' . mt_rand(),
        ];
        $message = new ArrayBased(['additionalHeaders' => $addHeaders]);

        $this->assertEquals(
            $addHeaders,
            $message->getAdditionalHeaders()
        );
    }
}
