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

use User\Form\PermissionForm;
use Laminas\Validator\AbstractValidator;
use User\Entity\Permission;

/**
 * This validator class is designed for checking if there is an existing permission with such a name
 * @package Application\Validator
 */
class PermissionExistsValidator extends AbstractValidator
{
    // Validation failure message IDs
    public const NOT_SCALAR  = 'notScalar';
    public const PERMISSION_EXISTS = 'permissionExists';

    /**
     * Available validator options
     * @var array $options
     */
    protected $options = [
        'entityManager' => null,
        'permission' => null
    ];

    /**
     * Validation failure messages
     * @var array $messageTemplates
     */
    protected array $messageTemplates = [
        self::NOT_SCALAR  => "The email must be a scalar value",
        self::PERMISSION_EXISTS  => "Another permission with such name already exists"
    ];

    /**
     * PermissionExistsValidator constructor
     * @param array|null $options
     */
    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            if (isset($options['entityManager'])) {
                $this->options['entityManager'] = $options['entityManager'];
            }
            if (isset($options['permission'])) {
                $this->options['permission'] = $options['permission'];
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
        $permission = $entityManager->getRepository(Permission::class)->findOneByName($value);
        $isValid = false;
        if ($this->options['permission'] == null) {
            if ($this->options['scenario'] == PermissionForm::SCENARIO_CREATE) {
                $isValid = true;
            } elseif ($this->options['scenario'] == PermissionForm::SCENARIO_UPDATE) {
                $isValid = ($permission == null);
            }
        } else {
            if ($this->options['scenario'] == PermissionForm::SCENARIO_CREATE) {
                $isValid = ($this->options['permission']->getName() != $value && $permission != null);
            } elseif ($this->options['scenario'] == PermissionForm::SCENARIO_UPDATE) {
                $isValid = true;
            }
        }
        if (! $isValid) {
            $this->error(self::PERMISSION_EXISTS);
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
