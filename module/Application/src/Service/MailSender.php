<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Laminas Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2021-2022 scorpion3dd
 */

declare(strict_types=1);

namespace Application\Service;

use Exception;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\Log\Logger;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Sendmail;

/**
 * This service class is used to deliver an E-mail message to recipient.
 * @package User\Service
 */
class MailSender extends AbstractService
{
    /** @var Sendmail $transport */
    private Sendmail $transport;

    /**
     * MailSender constructor
     * @param ContainerInterface $container
     * @param Sendmail $transport
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container, Sendmail $transport)
    {
        parent::__construct($container);
        $this->transport = $transport;
    }

    /**
     * @param string $sender
     * @param string $recipient
     * @param string $subject
     * @param string $text
     *
     * @return bool
     */
    public function sendMail(string $sender, string $recipient, string $subject, string $text): bool
    {
        try {
            $mail = new Message();
            $mail->setBody($text);
            $mail->setFrom($sender);
            $mail->addTo($recipient);
            $mail->setSubject($subject);

            $this->getTransport()->setParameters('-f' . $sender);
            $this->getTransport()->send($mail);
            $result = true;
        } catch (Exception $e) {
            $this->logger->log(Logger::ERR, $e->getMessage());
            $result = false;
        }

        return $result;
    }

    /**
     * @return Sendmail
     */
    public function getTransport(): Sendmail
    {
        return $this->transport;
    }

    /**
     * @param Sendmail $transport
     */
    public function setTransport(Sendmail $transport): void
    {
        $this->transport = $transport;
    }
}
