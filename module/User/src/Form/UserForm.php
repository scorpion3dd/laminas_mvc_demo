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
use User\Entity\User;
use Laminas\Form\Form;
use Laminas\InputFilter\ArrayInput;
use User\Validator\UserExistsValidator;
use Laminas\Validator\Hostname;

/**
 * This form is used to collect user's email, full name, password and status. The form
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario he/she doesn't enter password.
 * @package User\Form
 */
class UserForm extends Form
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';

    /** @var string $scenario */
    private string $scenario;

    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var User|null $user */
    private ?User $user;

    /**
     * UserForm constructor
     * @param string $scenario
     * @param EntityManager|null $entityManager
     * @param User|null $user
     */
    public function __construct(string $scenario = 'create', EntityManager $entityManager = null, ?User $user = null)
    {
        parent::__construct('user-form');
        $this->setAttribute('method', 'post');
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->user = $user;
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
            'name' => 'email',
            'options' => [
                'label' => 'E-mail',
            ],
        ]);
        $this->add([
            'type'  => 'text',
            'name' => 'full_name',
            'options' => [
                'label' => 'Full Name',
            ],
        ]);
        $this->add([
            'type'  => 'text',
            'name' => 'description',
            'options' => [
                'label' => 'Description',
            ],
        ]);
        $this->add([
            'type'  => 'date',
            'name' => 'date_birthday',
            'options' => [
                'label' => 'Date Birthday',
            ],
        ]);
        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->add([
                'type'  => 'password',
                'name' => 'password',
                'options' => [
                    'label' => 'Password',
                ],
            ]);
            $this->add([
                'type'  => 'password',
                'name' => 'confirm_password',
                'options' => [
                    'label' => 'Confirm password',
                ],
            ]);
        }
        $this->add([
            'type'  => 'select',
            'name' => 'status',
            'options' => [
                'label' => 'Status',
                'empty_option' => 'Please choose',
                'value_options' => [
                    User::STATUS_ACTIVE_ID => User::STATUS_ACTIVE,
                    User::STATUS_DISACTIVE_ID => User::STATUS_DISACTIVE,
                ]
            ],
        ]);
        $this->add([
            'type'  => 'select',
            'name' => 'access',
            'options' => [
                'label' => 'Access',
                'empty_option' => 'Please choose',
                'value_options' => [
                    User::ACCESS_YES_ID => User::ACCESS_YES,
                    User::ACCESS_NO_ID => User::ACCESS_NO,
                ]
            ],
        ]);
        $this->add([
            'type'  => 'select',
            'name' => 'gender',
            'options' => [
                'label' => 'Gender',
                'empty_option' => 'Please choose',
                'value_options' => [
                    User::GENDER_MALE_ID => User::GENDER_MALE,
                    User::GENDER_FEMALE_ID => User::GENDER_FEMALE,
                ]
            ],
        ]);
        $this->add([
            'type'  => 'select',
            'name' => 'roles',
            'attributes' => [
                'multiple' => 'multiple',
            ],
            'options' => [
                'label' => 'Role(s)',
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
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Create'
            ],
            'options' => [
                'label' => 'Create',
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
                'name'     => 'email',
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
                        'name' => 'EmailAddress',
                        'options' => [
                            'allow' => Hostname::ALLOW_DNS,
                            'useMxCheck' => false,
                        ],
                    ],
                    [
                        'name' => UserExistsValidator::class,
                        'options' => [
                            'entityManager' => $this->entityManager,
                            'user' => $this->user,
                            'scenario' => $this->scenario
                        ],
                    ],
                ],
            ]);
        $inputFilter->add([
                'name'     => 'full_name',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 512
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
                            'min' => 1,
                            'max' => 512
                        ],
                    ],
                ],
            ]);
        $inputFilter->add([
            'name'     => 'date_birthday',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'Date',
                    'options' => [
                        'format' => 'Y-m-d'
                    ],
                ],
            ],
        ]);
        if ($this->scenario == self::SCENARIO_CREATE) {
            $inputFilter->add([
                    'name'     => 'password',
                    'required' => true,
                    'filters'  => [
                    ],
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'min' => 6,
                                'max' => 64
                            ],
                        ],
                    ],
                ]);
            $inputFilter->add([
                    'name'     => 'confirm_password',
                    'required' => true,
                    'filters'  => [
                    ],
                    'validators' => [
                        [
                            'name'    => 'Identical',
                            'options' => [
                                'token' => 'password',
                            ],
                        ],
                    ],
                ]);
        }
        $inputFilter->add([
                'name'     => 'status',
                'required' => true,
                'filters'  => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [
                    ['name' => 'InArray', 'options' => ['haystack' => [1, 2]]]
                ],
            ]);
        $inputFilter->add([
            'name'     => 'access',
            'required' => true,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'InArray', 'options' => ['haystack' => [1, 2]]]
            ],
        ]);
        $inputFilter->add([
            'name'     => 'gender',
            'required' => true,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'InArray', 'options' => ['haystack' => [1, 2]]]
            ],
        ]);
        $inputFilter->add([
                'class'    => ArrayInput::class,
                'name'     => 'roles',
                'required' => true,
                'filters'  => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [
                    ['name' => 'GreaterThan', 'options' => ['min' => 0]]
                ],
            ]);
    }
}
