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

namespace User\Form;

use Doctrine\ORM\EntityManager;
use User\Entity\Role;
use Laminas\Form\Form;
use User\Validator\RoleExistsValidator;

/**
 * The form for collecting information about Role
 * @package User\Form
 */
class RoleForm extends Form
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';

    /** @var string $scenario */
    private string $scenario;

    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var Role|null $role */
    private ?Role $role;

    /**
     * RoleForm constructor
     * @param string $scenario
     * @param EntityManager|null $entityManager
     * @param Role|null $role
     */
    public function __construct(
        string $scenario = 'create',
        EntityManager $entityManager = null,
        Role $role = null
    ) {
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->role = $role;
        parent::__construct('role-form');
        $this->setAttribute('method', 'post');
        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button)
     *
     * @return void
     */
    protected function addElements(): void
    {
        $this->add([
            'type'  => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name'
            ],
            'options' => [
                'label' => 'Role Name',
            ],
        ]);
        $this->add([
            'type'  => 'textarea',
            'name' => 'description',
            'attributes' => [
                'id' => 'description'
            ],
            'options' => [
                'label' => 'Description',
            ],
        ]);
        $this->add([
            'type'  => 'select',
            'name' => 'inherit_roles',
            'attributes' => [
                'id' => 'inherit_roles',
                'multiple' => 'multiple',
            ],
            'options' => [
                'label' => 'Optionally inherit permissions from these role(s)'
            ],
        ]);
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Create',
                'id' => 'submit',
            ],
        ]);
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation)
     *
     * @return void
     */
    private function addInputFilter(): void
    {
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
                'name'     => 'name',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                    [
                        'name' => RoleExistsValidator::class,
                        'options' => [
                            'entityManager' => $this->entityManager,
                            'role' => $this->role,
                            'scenario' => $this->scenario
                        ],
                    ],
                ],
            ]);
        $inputFilter->add([
                'name'     => 'description',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 0,
                            'max' => 1024
                        ],
                    ],
                ],
            ]);
        $inputFilter->add([
                'name'     => 'inherit_roles',
                'required' => false,
                'filters'  => [],
                'validators' => [],
            ]);
    }
}
