<?php

namespace AtAdmin\Form;

use AtDataGrid\DataGrid;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Http\PhpEnvironment\Request;
use ZfcBase\EventManager\EventProvider;

class FormManager extends EventProvider
{
    const EVENT_GRID_FORM_BUILD_POST = 'at-datagrid.grid.form.build.post';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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
}