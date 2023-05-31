<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Zend Framework 3 Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2020-2021 scorpion3dd
 */

declare(strict_types=1);

namespace ApplicationTest\unit\Service;

use Application\Service\MailSender;
use ApplicationTest\AbstractMock;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Entity\User;
use Laminas\Mail\Transport\Sendmail;

/**
 * Class MailSenderTest - Unit tests for MailSender
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Service
 */
class MailSenderTest extends AbstractMock
{
    /** @var MailSender $mailSender */
    public MailSender $mailSender;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mailSender = $this->serviceManager->get(MailSender::class);
    }

    /**
     * @testCase - method sendMail - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testSendMail(): void
    {
        $sender = User::EMAIL_ADMIN;
        $recipient = self::USER_EMAIL;
        $subject = 'Example subject';
        $text = 'Example text';

        $sendmailMock = $this->getMockBuilder(Sendmail::class)
            ->onlyMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $sendmailMock->expects(self::exactly(1))
            ->method('send');

        $this->mailSender->setTransport($sendmailMock);

        $result = $this->mailSender->sendMail($sender, $recipient, $subject, $text);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method sendMail - Exception
     * recipient is not a valid email address
     *
     * @return void
     * @throws Exception
     */
    public function testSendMailException(): void
    {
        $sender = User::EMAIL_ADMIN;
        $recipient = 'Example email recipient';
        $subject = 'Example subject';
        $text = 'Example text';
        $result = $this->mailSender->sendMail($sender, $recipient, $subject, $text);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method translate - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testTranslate(): void
    {
        $result = $this->mailSender->translate('submit');
        $this->assertEquals('submit', $result);
    }
}
