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

namespace User\Enum;

/**
 * @package User\Enum
 */
class Queue
{
    public const EMAIL_SUBJECT = 'User notification';
    public const EMAIL_SEND_QUEUE = 'email_send_queue';
    public const USER_NOTIFICATION = 'user_notification';
    public const POSTBACK_QUEUE = 'postback_queue';
}
