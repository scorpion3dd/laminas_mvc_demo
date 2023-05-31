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

namespace User\Validator;

use User\Form\UserForm;
use Laminas\Validator\AbstractValidator;
use User\Entity\User;

/**
 * This validator class is designed for checking if there is an existing user with such an email
 * @package Application\Validator
 */
class UserExistsValidator extends AbstractValidator
{
    // Validation failure message IDs
    public const NOT_SCALAR  = 'notScalar';
    public const USER_EXISTS = 'userExists';

    /**
     * Available validator options
     * @var array $options
     */
    public array $options = [
        'entityManager' => null,
        'user' => null
    ];

    /**
     * Validation failure messages
     * @var array $messageTemplates
     */
    protected array $messageTemplates = [
        self::NOT_SCALAR  => "The email must be a scalar value",
        self::USER_EXISTS  => "Another user with such an email already exists"
    ];

    /**
     * UserExistsValidator constructor
     * @param array|null $options
     */
    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            if (isset($options['entityManager'])) {
                $this->options['entityManager'] = $options['entityManager'];
            }
            if (isset($options['user'])) {
                $this->options['user'] = $options['user'];
            }
        }
        parent::__construct($options);
    }

    /**
     * Check if user exists
     * @param mixed $value
     *
     * @return bool
     */
    public function isValid(mixed $value): bool
    {
        if (! is_scalar($value)) {
            $this->error(self::NOT_SCALAR);

            return false;
        }
        $entityManager = $this->options['entityManager'];
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $value]);
        $isValid = false;
        if ($this->options['user'] == null) {
            if ($this->options['scenario'] == UserForm::SCENARIO_CREATE) {
                $isValid = true;
            } elseif ($this->options['scenario'] == UserForm::SCENARIO_UPDATE) {
                $isValid = ($user == null);
            }
        } else {
            if ($this->options['scenario'] == UserForm::SCENARIO_CREATE) {
                $isValid = ($this->options['user']->getEmail() != $value && $user != null);
            } elseif ($this->options['scenario'] == UserForm::SCENARIO_UPDATE) {
                $isValid = true;
            }
        }
        if (! $isValid) {
            $this->error(self::USER_EXISTS);
        }

        return $isValid;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return AbstractValidator
     */
    public function setOptions($options = [])
    {
        $this->options = $options;

        return $this;
    }
}
