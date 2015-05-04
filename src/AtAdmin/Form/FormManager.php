<?php

namespace AtAdmin\Form;

use AtDataGrid\DataGrid;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\Element;

class FormManager
{
    use EventManagerAwareTrait;

    const FORM_CONTEXT_PARAM_NAME = '__context';

    const FORM_CONTEXT_CREATE = 'create';
    const FORM_CONTEXT_EDIT   = 'edit';

    const EVENT_GRID_FORM_BUILD_PRE = 'at-admin.grid.form.build.pre';
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
    protected $formSections = array();

    /**
     * @param DataGrid $grid
     * @param string $context
     * @param array $data
     * @return Form
     */
    public function buildFormFromGrid(DataGrid $grid, $context = self::FORM_CONTEXT_CREATE, $data = [])
    {
        if (array_key_exists($context, $this->forms)) {
            return $this->forms[$context];
        }

        $data[self::FORM_CONTEXT_PARAM_NAME] = $context;
        $this->getEventManager()->trigger(self::EVENT_GRID_FORM_BUILD_PRE, null, $data);

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

        // Add section elements
        $formSections = $this->getFormSections();

        foreach ($formSections as $name => $section) {
            $fieldsSet = new Fieldset($name, ['label' => $section['label']]);
            foreach ($section['elements'] as $element) {
                $fieldsSet->add($element);
            }
            $form->add($fieldsSet);
        }

        // Submit button
        $submit = new Element\Submit('submit');
        $submit->setValue('Save');
        $form->add($submit);

        $this->getEventManager()->trigger(self::EVENT_GRID_FORM_BUILD_POST, $form, $data);

        // Set data to form
        if ($data) {
            $form->setData($data);
        }

        $this->forms[$context] = $form;

        return $form;
    }

    /**
     * @return array
     */
    public function getFormSections()
    {
        return $this->formSections;
    }

    /**
     * @param $name
     * @param $options
     * @return $this
     */
    public function addFormSection($name, $options)
    {
        $this->formSections[$name] = $options;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeFormSection($name)
    {
        if (isset($this->formSections[$name])) {
            unset($this->formSections[$name]);
        }

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function clearFormSectionElements($name)
    {
        if (isset($this->formSections[$name])) {
            $this->formSections[$name]['elements'] = [];
        }

        return $this;
    }

    /**
     * @param $sectionName
     * @param $element
     * @return $this
     * @throws \Exception
     */
    public function addFormSectionElement($sectionName, $element)
    {
        if (! array_key_exists($sectionName, $this->formSections)) {
            throw new \Exception('No tab with name "'. $sectionName .'"');
        }

        //$elementName = $sectionName . '[' . $element->getName() . ']';
        //$element->setName($elementName);

        $this->formSections[$sectionName]['elements'][$element->getName()] = $element;
        return $this;
    }
}