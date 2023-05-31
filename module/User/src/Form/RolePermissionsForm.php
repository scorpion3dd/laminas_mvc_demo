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
use Laminas\Form\Fieldset;

/**
 * The form for collecting information about permissions assigned to a role
 * @package User\Form
 */
class RolePermissionsForm extends Form
{
    /**
     * RolePermissionsForm constructor
     */
    public function __construct()
    {
        parent::__construct('role-permissions-form');
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
        $fieldset = new Fieldset('permissions');
        $this->add($fieldset);
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
     * @param string $name
     * @param string $label
     * @param bool $isDisabled
     *
     * @return void
     */
    public function addPermissionField(string $name, string $label, bool $isDisabled = false): void
    {
        /** @var Fieldset $element1 */
        $element1 = $this->get('permissions');
        $element1->add([
            'type'  => 'checkbox',
            'name' => $name,
            'attributes' => [
                'id' => $name,
                'disabled' => $isDisabled
            ],
            'options' => [
                'label' => $label
            ],
        ]);
        /** @var Fieldset $element2 */
        $element2 = $this->getInputFilter()->get('permissions');
        $element2->add([
                'name'     => $name,
                'required' => false,
                'filters'  => [
                ],
                'validators' => [
                    ['name' => 'IsInt'],
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
    }
}
