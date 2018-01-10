<?php

namespace marvin255\bxmailer\tests\message;

use marvin255\bxmailer\tests\BaseTestCase;
use marvin255\bxmailer\handler\PhpMailer;

class PhpMailerTest extends BaseTestCase
{
    public function testSend()
    {
        $subject = 'subject_' . mt_rand();
        $body = 'body_' . mt_rand();
        $from = 'from_' . mt_rand();
        $replyTo = 'reply_to_' . mt_rand();
        $isHtml = true;
        $to = ['to_1_' . mt_rand(), 'to_2_' . mt_rand()];
        $cc = ['cc_1_' . mt_rand(), 'cc_2_' . mt_rand()];
        $bcc = ['bcc_1_' . mt_rand(), 'bcc_2_' . mt_rand()];
        $headers = [
            'header_1_' . mt_rand() => 'value_1_' . mt_rand(),
            'header_2_' . mt_rand() => 'value_2_' . mt_rand(),
        ];
        $attachments = [
            'name_1' => 'path_1_' . mt_rand(),
            'name_2' => 'path_2_' . mt_rand(),
        ];

        $message = $this->getMockBuilder('\marvin255\bxmailer\MessageInterface')->getMock();
        $message->method('getSubject')->will($this->returnValue($subject));
        $message->method('getMessage')->will($this->returnValue($body));
        $message->method('getFrom')->will($this->returnValue($from));
        $message->method('getReplyTo')->will($this->returnValue($replyTo));
        $message->method('isHtml')->will($this->returnValue($isHtml));
        $message->method('getTo')->will($this->returnValue($to));
        $message->method('getCc')->will($this->returnValue($cc));
        $message->method('getBcc')->will($this->returnValue($bcc));
        $message->method('getAdditionalHeaders')->will($this->returnValue($headers));
        $message->method('getAttachments')->will($this->returnValue($attachments));

        $phpMailer = $this->getMockBuilder('\PHPMailer\PHPMailer\PHPMailer')->getMock();
        $phpMailer->expects($this->never())->method('isSMTP');
        $phpMailer->expects($this->once())->method('clearAddresses');
        $phpMailer->expects($this->once())->method('clearCCs');
        $phpMailer->expects($this->once())->method('clearBCCs');
        $phpMailer->expects($this->once())->method('clearReplyTos');
        $phpMailer->expects($this->once())->method('clearCustomHeaders');
        $phpMailer->expects($this->once())->method('clearAttachments');
        $phpMailer->expects($this->once())->method('setFrom')->with($this->equalTo($from));
        $phpMailer->expects($this->once())->method('addReplyTo')->with($this->equalTo($replyTo));
        $phpMailer->expects($this->once())->method('isHtml')->with($this->equalTo($isHtml));
        $phpMailer->expects($this->once())->method('send')->will($this->returnValue(true));
        $phpMailer->expects($this->never())->method('smtpClose');

        $mailerTo = [];
        $phpMailer->method('addAddress')->will($this->returnCallback(function ($item) use (&$mailerTo) {
            $mailerTo[] = $item;
        }));

        $mailerCc = [];
        $phpMailer->method('addCC')->will($this->returnCallback(function ($item) use (&$mailerCc) {
            $mailerCc[] = $item;
        }));

        $mailerBcc = [];
        $phpMailer->method('addBCC')->will($this->returnCallback(function ($item) use (&$mailerBcc) {
            $mailerBcc[] = $item;
        }));

        $mailerHeaders = [];
        $phpMailer->method('addCustomHeader')->will($this->returnCallback(function ($item) use (&$mailerHeaders) {
            $arItem = array_map('trim', explode(':', $item));
            $mailerHeaders[$arItem[0]] = $arItem[1] ?: null;
        }));

        $mailerAttachments = [];
        $phpMailer->method('addAttachment')->will($this->returnCallback(function ($path, $name) use (&$mailerAttachments) {
            $mailerAttachments[$name] = $path;
        }));

        $options = $this->getMockBuilder('\marvin255\bxmailer\OptionsInterface')->getMock();

        $mailer = new PhpMailer($phpMailer, $options);

        $this->assertSame(true, $mailer->send($message));
        $this->assertSame($subject, $phpMailer->Subject);
        $this->assertSame($body, $phpMailer->Body);
        $this->assertSame($to, $mailerTo);
        $this->assertSame($cc, $mailerCc);
        $this->assertSame($bcc, $mailerBcc);
        $this->assertSame($headers, $mailerHeaders);
        $this->assertSame($attachments, $mailerAttachments);
    }

    public function testSendSmtp()
    {
        $phpMailer = $this->getMockBuilder('\PHPMailer\PHPMailer\PHPMailer')->getMock();
        $phpMailer->method('send')->will($this->returnValue(true));
        $phpMailer->expects($this->once())->method('isSMTP');
        $phpMailer->expects($this->once())->method('smtpClose');

        $message = $this->getMockBuilder('\marvin255\bxmailer\MessageInterface')->getMock();
        $message->method('getTo')->will($this->returnValue([]));
        $message->method('getCc')->will($this->returnValue([]));
        $message->method('getBcc')->will($this->returnValue([]));
        $message->method('getAdditionalHeaders')->will($this->returnValue([]));
        $message->method('getAttachments')->will($this->returnValue([]));

        $options = [
            'is_smtp' => true,
            'smtp_timeout' => mt_rand(),
            'smtp_host' => 'host_' . mt_rand(),
            'smtp_login' => 'login_' . mt_rand(),
            'smtp_password' => 'password_' . mt_rand(),
            'smtp_secure' => 'secure_' . mt_rand(),
            'smtp_port' => mt_rand(),
            'smtp_auth' => false,
            'charset' => 'charset_' . mt_rand(),
        ];
        $optionsBag = $this->getMockBuilder('\marvin255\bxmailer\OptionsInterface')->getMock();
        $optionsBag->method('getInt')->will($this->returnCallback(function ($name, $def) use ($options) {
            return isset($options[$name]) ? $options[$name] : $def;
        }));
        $optionsBag->method('getBool')->will($this->returnCallback(function ($name, $def) use ($options) {
            return isset($options[$name]) ? $options[$name] : $def;
        }));
        $optionsBag->method('get')->will($this->returnCallback(function ($name, $def) use ($options) {
            return isset($options[$name]) ? $options[$name] : $def;
        }));

        $mailer = new PhpMailer($phpMailer, $optionsBag);

        $mailer->setDebug(false);
        $mailer->send($message);

        $this->assertSame($options['smtp_timeout'], $phpMailer->Timeout, 'smtp_timeout option');
        $this->assertSame($options['smtp_host'], $phpMailer->Host, 'smtp_host option');
        $this->assertSame($options['smtp_login'], $phpMailer->Username, 'smtp_login option');
        $this->assertSame($options['smtp_password'], $phpMailer->Password, 'smtp_password option');
        $this->assertSame($options['smtp_port'], $phpMailer->Port, 'smtp_port option');
        $this->assertSame($options['smtp_secure'], $phpMailer->SMTPSecure, 'smtp_secure option');
        $this->assertSame($options['charset'], $phpMailer->CharSet, 'charset option');
        $this->assertSame(false, $phpMailer->SMTPAuth, 'SMTPAuth option');
        $this->assertSame(false, $phpMailer->SMTPAutoTLS, 'SMTPAutoTLS option');
        $this->assertSame(0, $phpMailer->SMTPDebug, 'SMTPDebug option');
    }

    public function testSendSmtpDebug()
    {
        $phpMailer = $this->getMockBuilder('\PHPMailer\PHPMailer\PHPMailer')->getMock();
        $phpMailer->method('send')->will($this->returnValue(true));
        $phpMailer->expects($this->once())->method('isSMTP');

        $message = $this->getMockBuilder('\marvin255\bxmailer\MessageInterface')->getMock();
        $message->method('getTo')->will($this->returnValue([]));
        $message->method('getCc')->will($this->returnValue([]));
        $message->method('getBcc')->will($this->returnValue([]));
        $message->method('getAdditionalHeaders')->will($this->returnValue([]));
        $message->method('getAttachments')->will($this->returnValue([]));

        $options = [
            'is_smtp' => true,
            'smtp_timeout' => mt_rand(),
            'smtp_host' => 'host_' . mt_rand(),
            'smtp_login' => 'login_' . mt_rand(),
            'smtp_password' => 'password_' . mt_rand(),
            'smtp_secure' => 'secure_' . mt_rand(),
            'smtp_port' => mt_rand(),
            'smtp_auth' => false,
            'charset' => 'charset_' . mt_rand(),
        ];
        $optionsBag = $this->getMockBuilder('\marvin255\bxmailer\OptionsInterface')->getMock();
        $optionsBag->method('getInt')->will($this->returnCallback(function ($name, $def) use ($options) {
            return isset($options[$name]) ? $options[$name] : $def;
        }));
        $optionsBag->method('getBool')->will($this->returnCallback(function ($name, $def) use ($options) {
            return isset($options[$name]) ? $options[$name] : $def;
        }));
        $optionsBag->method('get')->will($this->returnCallback(function ($name, $def) use ($options) {
            return isset($options[$name]) ? $options[$name] : $def;
        }));

        $mailer = new PhpMailer($phpMailer, $optionsBag);

        $mailer->setDebug(true);
        $mailer->send($message);

        $this->assertSame($options['smtp_timeout'], $phpMailer->Timeout, 'smtp_timeout option');
        $this->assertSame($options['smtp_host'], $phpMailer->Host, 'smtp_host option');
        $this->assertSame($options['smtp_login'], $phpMailer->Username, 'smtp_login option');
        $this->assertSame($options['smtp_password'], $phpMailer->Password, 'smtp_password option');
        $this->assertSame($options['smtp_port'], $phpMailer->Port, 'smtp_port option');
        $this->assertSame($options['smtp_secure'], $phpMailer->SMTPSecure, 'smtp_secure option');
        $this->assertSame($options['charset'], $phpMailer->CharSet, 'charset option');
        $this->assertSame(false, $phpMailer->SMTPAuth, 'SMTPAuth option');
        $this->assertSame(false, $phpMailer->SMTPAutoTLS, 'SMTPAutoTLS option');
        $this->assertSame(2, $phpMailer->SMTPDebug, 'SMTPDebug option');
    }

    public function testSendException()
    {
        $exc = 'exception_' . mt_rand();

        $phpMailer = $this->getMockBuilder('\PHPMailer\PHPMailer\PHPMailer')->getMock();
        $phpMailer->method('send')->will($this->throwException(new \PHPMailer\PHPMailer\Exception));
        $phpMailer->ErrorInfo = $exc;

        $optionsBag = $this->getMockBuilder('\marvin255\bxmailer\OptionsInterface')->getMock();

        $message = $this->getMockBuilder('\marvin255\bxmailer\MessageInterface')->getMock();
        $message->method('getTo')->will($this->returnValue([]));
        $message->method('getCc')->will($this->returnValue([]));
        $message->method('getBcc')->will($this->returnValue([]));
        $message->method('getAdditionalHeaders')->will($this->returnValue([]));
        $message->method('getAttachments')->will($this->returnValue([]));

        $mailer = new PhpMailer($phpMailer, $optionsBag);

        $this->setExpectedException('\marvin255\bxmailer\Exception', $exc);
        $mailer->send($message);
    }

    public function testGetDebug()
    {
        $phpMailer = $this->getMockBuilder('\PHPMailer\PHPMailer\PHPMailer')->getMock();
        $optionsBag = $this->getMockBuilder('\marvin255\bxmailer\OptionsInterface')->getMock();
        $mailer = new PhpMailer($phpMailer, $optionsBag);

        $this->assertSame(false, $mailer->getDebug(), 'default debug status');
        $this->assertSame($mailer, $mailer->setDebug(), 'return self after setDebug');
        $this->assertSame(true, $mailer->getDebug(), 'set debug');
    }
}
