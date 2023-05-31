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

use User\Form\RoleForm;
use Laminas\Validator\AbstractValidator;
use User\Entity\Role;

/**
 * This validator class is designed for checking if there is an existing role with such a name
 * @package Application\Validator
 */
class RoleExistsValidator extends AbstractValidator
{
    // Validation failure message IDs
    public const NOT_SCALAR  = 'notScalar';
    public const ROLE_EXISTS = 'roleExists';

    /**
     * Available validator options
     * @var array $options
     */
    protected $options = [
        'entityManager' => null,
        'role' => null
    ];

    /**
     * Validation failure messages
     * @var array $messageTemplates
     */
    protected array $messageTemplates = [
        self::NOT_SCALAR  => "The email must be a scalar value",
        self::ROLE_EXISTS  => "Another role with such name already exists"
    ];

    /**
     * RoleExistsValidator constructor
     * @param array|null $options
     */
    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            if (isset($options['entityManager'])) {
                $this->options['entityManager'] = $options['entityManager'];
            }
            if (isset($options['role'])) {
                $this->options['role'] = $options['role'];
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
        $role = $entityManager->getRepository(Role::class)->findOneByName($value);
        $isValid = false;
        if ($this->options['role'] == null) {
            if ($this->options['scenario'] == RoleForm::SCENARIO_CREATE) {
                $isValid = true;
            } elseif ($this->options['scenario'] == RoleForm::SCENARIO_UPDATE) {
                $isValid = ($role == null);
            }
        } else {
            if ($this->options['scenario'] == RoleForm::SCENARIO_CREATE) {
                $isValid = ($this->options['role']->getName() != $value && $role != null);
            } elseif ($this->options['scenario'] == RoleForm::SCENARIO_UPDATE) {
                $isValid = true;
            }
        }
        if (! $isValid) {
            $this->error(self::ROLE_EXISTS);
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
