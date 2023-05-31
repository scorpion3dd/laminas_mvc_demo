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
use Laminas\Validator\Hostname;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag
 * @package User\Form
 */
class LoginForm extends Form
{
    /**
     * LoginForm constructor
     */
    public function __construct()
    {
        parent::__construct('login-form');
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
            'name' => 'email',
            'options' => [
                'label' => 'Your E-mail',
            ],
        ]);
        $this->add([
            'type'  => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'type'  => 'checkbox',
            'name' => 'remember_me',
            'options' => [
                'label' => 'Remember me',
            ],
        ]);
        $this->add([
            'type'  => 'hidden',
            'name' => 'redirect_url'
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
                'value' => 'Sign in',
                'id' => 'submit',
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
                        'name' => 'EmailAddress',
                        'options' => [
                            'allow' => Hostname::ALLOW_DNS,
                            'useMxCheck' => false,
                        ],
                    ],
                ],
            ]);
        $inputFilter->add([
                'name'     => 'password',
                'required' => true,
                'filters'  => [
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 64
                        ],
                    ],
                ],
            ]);
        $inputFilter->add([
                'name'     => 'remember_me',
                'required' => false,
                'filters'  => [
                ],
                'validators' => [
                    [
                        'name'    => 'InArray',
                        'options' => [
                            'haystack' => [0, 1],
                        ]
                    ],
                ],
            ]);
        $inputFilter->add([
                'name'     => 'redirect_url',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 0,
                            'max' => 2048
                        ]
                    ],
                ],
            ]);
    }
}
