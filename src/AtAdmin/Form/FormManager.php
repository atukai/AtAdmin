<?php

namespace AtAdmin\Form;

use AtDataGrid\DataGrid;
use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\EventManager\EventProvider;

class FormManager extends EventProvider
{
    const EVENT_GRID_FORM_BUILD_POST = 'at-admin.grid.form.build.post';

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var array
     */
    protected $formTabs = array();

    /**
     * @param DataGrid $grid
     * @return Form
     */
    public function getForm(DataGrid $grid)
    {
        if (!$this->form) {
            $this->buildFormFromGrid($grid);
        }

        return $this->form;
    }

    /**
     * @param DataGrid $grid
     * @return Form
     */
    protected function buildFormFromGrid(DataGrid $grid)
    {
        $form = new Form('at-datagrid-form-create');

        // Collect elements
        foreach ($grid->getColumns() as $column) {
            if (!$column->isVisibleInForm()) {
                continue;
            }

            /* @var \Zend\Form\Element */
            $element = $column->getFormElement();
            //$element->setName($column->getName());

            if (!$element->getLabel()) {
                $element->setLabel($column->getLabel());
            }

            $form->add($element);
        }

        // Hash element to prevent CSRF attack
        $csrf = new Element\Csrf('hash');
        $form->add($csrf);

        // Submit button
        $submit = new Element\Submit('submit');
        $submit->setValue('Save');
        $form->add($submit);

        $this->getEventManager()->trigger(self::EVENT_GRID_FORM_BUILD_POST, $form);

        $this->form = $form;

        return $this->form;
    }

    /**
     * @return array
     */
    public function getFormTabs()
    {
        return $this->formTabs;
    }

    /**
     * @param $name
     * @param $options
     * @return $this
     */
    public function addFormTab($name, $options)
    {
        $this->formTabs[$name] = $options;
        return $this;
    }

    /**
     * @param $tabName
     * @param $element
     * @return $this
     */
    public function addFormTabElement($tabName, $element)
    {
        $elementName = $tabName . '[' . $element->getName() . ']';
        $element->setName($elementName);

        $this->formTabs[$tabName]['elements'][$element->getName()] = $element;
        return $this;
    }
}