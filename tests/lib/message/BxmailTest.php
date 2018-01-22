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
            mb_encode_mimeheader($subject),
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

    public function testGetMessageFromBoundedContent()
    {
        $messageText = 'message_' . mt_rand();
        $boundedMessage = "------------5a6581c454bf0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit

{$messageText}

------------5a6581c454bf0
Content-Type: text/plain; name=\"=?UTF-8?B?dGVzdC50eHQ=?=\"
Content-Transfer-Encoding: base64

dGVzdA==
------------5a6581c454bf0--
";
        $message = new Bxmail(
            'test@test.test',
            'test',
            $boundedMessage,
            "Content-Type: multipart/mixed; boundary=\"----------5a6581c454bf0\"\r\n"
        );

        $this->assertEquals(
            $messageText,
            $message->getMessage()
        );
    }

    public function testGetMessageFromBoundedContentException()
    {
        $message = new Bxmail(
            'test@test.test',
            'test',
            "------------5a6581c454bf0\n123123",
            "Content-Type: multipart/mixed; boundary=\"----------5a6581c454bf0\"\r\n"
        );

        $this->setExpectedException('\marvin255\bxmailer\Exception');
        $message->getMessage();
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

    public function testGetIsHtmlFromBoundedContent()
    {
        $messageText = 'message_' . mt_rand();
        $boundedMessage = "------------5a6581c454bf0
Content-Type: text/html; charset=UTF-8
Content-Transfer-Encoding: 8bit

{$messageText}

------------5a6581c454bf0
Content-Type: text/plain; name=\"=?UTF-8?B?dGVzdC50eHQ=?=\"
Content-Transfer-Encoding: base64

dGVzdA==
------------5a6581c454bf0--
";
        $message = new Bxmail(
            'test@test.test',
            'test',
            $boundedMessage,
            "Content-Type: multipart/mixed; boundary=\"----------5a6581c454bf0\"\r\n"
        );

        $this->assertEquals(
            true,
            $message->isHtml()
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

    public function testGetAttachments()
    {
        $attachments = [
            'key_1_' . mt_rand() => 'val_1_' . mt_rand(),
            'key_2_' . mt_rand() => 'val_2_' . mt_rand(),
        ];

        $header = '';
        foreach ($attachments as $key => $value) {
            if ($header !== '') {
                $header .= ';';
            }
            $header .= "{$value}=>{$key}";
        }

        $message = new Bxmail(
            'test@test.test',
            'test',
            'test',
            "CC: test@test.test\r\n"
                . 'ADD-FILE: ' . $header . "\r\n"
        );

        $this->assertEquals(
            $attachments,
            $message->getAttachments()
        );
    }

    public function testGetAttachmentsBoundedContent()
    {
        $boundedMessage = '------------5a6581c454bf0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit

test

------------5a6581c454bf0
Content-Type: text/plain; name="=?UTF-8?B?dGVzdC50eHQ=?="
Content-Transfer-Encoding: base64

dGVzdA==

------------5a6581c454bf0
Content-Type: text/plain
Content-Transfer-Encoding: base64

dGVzdA==
------------5a6581c454bf0--
';
        $message = new Bxmail(
            'test@test.test',
            'test',
            $boundedMessage,
            "Content-Type: multipart/mixed; boundary=\"----------5a6581c454bf0\"\r\n"
        );

        $attachments = $message->getAttachments();

        $this->assertCount(2, $attachments, 'number of files in attachment');
        $this->assertArrayHasKey('test.txt', $attachments, 'name from header');
        foreach ($attachments as $name => $path) {
            $this->assertFileExists($path, 'file must exists');
        }
    }
}
