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
 * This form is used to collect user's E-mail address (used to recover password)
 * @package User\Form
 */
class PasswordResetForm extends Form
{
    /**
     * PasswordResetForm constructor
     */
    public function __construct()
    {
        parent::__construct('password-reset-form');
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
            'type'  => 'email',
            'name' => 'email',
            'options' => [
                'label' => 'Your E-mail',
            ],
        ]);
        if (! $this->isEnvTest()) {
            $this->add([
                'type' => 'captcha',
                'name' => 'captcha',
                'options' => [
                    'label' => 'Human check',
                    'captcha' => [
                        'class' => 'Image',
                        'imgDir' => 'public/img/captcha',
                        'suffix' => '.png',
                        'imgUrl' => '/img/captcha/',
                        'imgAlt' => 'CAPTCHA Image',
                        'font' => './data/font/thorne_shaded.ttf',
                        'fsize' => 24,
                        'width' => 350,
                        'height' => 100,
                        'expiration' => 600,
                        'dotNoiseLevel' => 40,
                        'lineNoiseLevel' => 3
                    ],
                ],
            ]);
        }
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
                'value' => 'Reset Password',
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
    }

    /**
     * @param string $env
     *
     * @return bool
     */
    protected function isEnvTest(string $env = 'TEST'): bool
    {
        $envIs = getenv('APPLICATION_ENV');
        $is = $envIs === $env;

        return (bool)$is;
    }
}
