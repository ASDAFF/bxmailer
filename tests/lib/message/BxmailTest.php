<?php

namespace marvin255\bxmailer\tests\message;

use marvin255\bxmailer\tests\BaseTestCase;
use marvin255\bxmailer\message\Bxmail;

class BxmailTest extends BaseTestCase
{
    public function testGetTo()
    {
        $emails = ['email_1_' . mt_rand(), 'email_2_' . mt_rand()];
        $message = new Bxmail(
            implode(' , ', $emails),
            'test',
            'test'
        );

        $this->assertEquals(
            $emails,
            $message->getTo()
        );
    }

    public function testGetCc()
    {
        $emails = ['email_1_' . mt_rand(), 'email_2_' . mt_rand()];
        $message = new Bxmail(
            'test@test.test',
            'test',
            'test',
            'CC: ' . implode(', ', $emails) . "\r\n"
                . "BCC: test@test.test\r\n"
        );

        $this->assertEquals(
            $emails,
            $message->getCc()
        );
    }

    public function testGetBcc()
    {
        $emails = ['email_1_' . mt_rand(), 'email_2_' . mt_rand()];
        $message = new Bxmail(
            'test@test.test',
            'test',
            'test',
            "CC: test@test.test\r\n"
                . 'BCC: ' . implode(', ', $emails) . "\r\n"
        );

        $this->assertEquals(
            $emails,
            $message->getBcc()
        );
    }

    public function testGetFrom()
    {
        $from = 'from_' . mt_rand();
        $message = new Bxmail(
            'test@test.test',
            'test',
            'test',
            "From: {$from}\r\n"
        );

        $this->assertEquals(
            $from,
            $message->getFrom()
        );
    }

    public function testGetReplyTo()
    {
        $replyTo = 'reply_' . mt_rand();
        $message = new Bxmail(
            'test@test.test',
            'test',
            'test',
            "Reply-To: {$replyTo}\r\n"
        );

        $this->assertEquals(
            $replyTo,
            $message->getReplyTo()
        );
    }

    public function testGetSubject()
    {
        $subject = 'subject_тема_' . mt_rand();
        $message = new Bxmail(
            'test@test.test',
            $subject,
            'test'
        );

        $this->assertEquals(
            $subject,
            $message->getSubject()
        );
    }

    public function testGetEncodedSubject()
    {
        $subject = 'subject_тема_' . mt_rand();
        $message = new Bxmail(
            'test@test.test',
            '=?UTF-8?B?' . base64_encode($subject) . '?=',
            'test'
        );

        $this->assertEquals(
            $subject,
            $message->getSubject()
        );
    }

    public function testGetMessage()
    {
        $body = 'message_' . mt_rand();
        $message = new Bxmail(
            'test@test.test',
            'test',
            $body
        );

        $this->assertEquals(
            $body,
            $message->getMessage()
        );
    }

    public function testGetIsHtml()
    {
        $message = new Bxmail(
            'test@test.test',
            'test',
            'test',
            "Content-Type: text/html\r\n"
        );
        $message2 = new Bxmail(
            'test@test.test',
            'test',
            'test'
        );

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
        $message = new Bxmail(
            'test@test.test',
            'test',
            'test',
            "BCC: test@test.test\r\n"
                . "CC: test@test.test\r\n"
                . "header_1: {$addHeaders['header_1']}\r\n"
                . "header_2: {$addHeaders['header_2']}\r\n"
        );

        $this->assertEquals(
            $addHeaders,
            $message->getAdditionalHeaders()
        );
    }
}
