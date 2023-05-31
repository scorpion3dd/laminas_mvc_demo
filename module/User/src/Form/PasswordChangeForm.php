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

use Laminas\Form\Form;

/**
 * This form is used when changing user's password (to collect user's old password
 * and new password) or when resetting user's password (when user forgot his password).
 * @package User\Form
 */
class PasswordChangeForm extends Form
{
    private const SCENARIO_CHANGE = 'change';

    /**
     * There can be two scenarios - 'change' or 'reset'
     * @var string $scenario
     */
    private string $scenario;

    /**
     * PasswordChangeForm constructor
     * @param string $scenario
     */
    public function __construct(string $scenario)
    {
        parent::__construct('password-change-form');
        $this->scenario = $scenario;
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
        if ($this->scenario == self::SCENARIO_CHANGE) {
            $this->add([
                'type'  => 'password',
                'name' => 'old_password',
                'options' => [
                    'label' => 'Old Password',
                ],
            ]);
        }
        $this->add([
            'type'  => 'password',
            'name' => 'new_password',
            'options' => [
                'label' => 'New Password',
            ],
        ]);
        $this->add([
            'type'  => 'password',
            'name' => 'confirm_new_password',
            'options' => [
                'label' => 'Confirm new password',
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
                'value' => 'Change Password'
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
        if ($this->scenario == self::SCENARIO_CHANGE) {
            $inputFilter->add([
                    'name'     => 'old_password',
                    'required' => true,
                    'filters'  => [],
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
        }
        $inputFilter->add([
                'name'     => 'new_password',
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
                'name'     => 'confirm_new_password',
                'required' => true,
                'filters'  => [],
                'validators' => [
                    [
                        'name'    => 'Identical',
                        'options' => [
                            'token' => 'new_password',
                        ],
                    ],
                ],
            ]);
    }
}
