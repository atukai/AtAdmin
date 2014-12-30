<?php

namespace AtAdmin\Form;

use AtDataGrid\DataGrid;
use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\EventManager\EventProvider;

class FormManager extends EventProvider
{
    const FORM_CONTEXT_CREATE = 'create';
    const FORM_CONTEXT_EDIT   = 'edit';

    const EVENT_GRID_FORM_BUILD_POST = 'at-admin.grid.form.build.post';

    /**
     * @var array
     */
    protected $forms = array();

    /**
     * @var DataGrid
     */
    protected $grid;

    /**
     * @var array
     */
    protected $formTabs = array();

    /**
     * @param DataGrid $grid
     * @param string $context
     * @return Form
     */
    public function buildFormFromGrid(DataGrid $grid, $context = self::FORM_CONTEXT_CREATE)
    {
        if (array_key_exists($context, $this->forms)) {
            return $this->forms[$context];
        }

        $form = new Form('at-datagrid-form');

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

        $this->getEventManager()->trigger(self::EVENT_GRID_FORM_BUILD_POST, $form, array('context' => $context));

        $this->forms[$context] = $form;

        return $form;
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