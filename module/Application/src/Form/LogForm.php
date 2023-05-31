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

namespace Application\Form;

use Application\Document\Log;
use Laminas\Form\Form;

/**
 * This form is used to collect log's message, priority, timestamp. The form
 * can work in two scenarios - 'create' and 'update'.
 * In 'create' scenario, user enters message, priority, timestamp,
 * in 'update' scenario he/she enters only message.
 * @package Application\Form
 */
class LogForm extends Form
{
    private const SCENARIO_CREATE = 'create';

    /** @var string $scenario */
    private string $scenario;

    /**
     * UserForm constructor
     * @param string $scenario
     */
    public function __construct(string $scenario = 'create')
    {
        parent::__construct('user-form');
        $this->setAttribute('method', 'post');
        $this->scenario = $scenario;
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
            'name' => 'message',
            'options' => [
                'label' => 'Message',
            ],
        ]);
        $this->add([
            'type'  => 'select',
            'name' => 'priority',
            'options' => [
                'label' => 'Priority',
                'value_options' => Log::getPriorities()
            ],
        ]);
        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->add([
                'type'  => 'date',
                'name' => 'timestamp',
                'options' => [
                    'label' => 'Timestamp',
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
                'name'     => 'message',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 256
                        ],
                    ],
                ],
            ]);
        $inputFilter->add([
            'name'     => 'priority',
            'required' => true,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'InArray', 'options' => ['haystack' => array_keys(Log::getPriorities())]]
            ],
        ]);
        if ($this->scenario == self::SCENARIO_CREATE) {
            $inputFilter->add([
                'name'     => 'timestamp',
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
        }
    }
}
