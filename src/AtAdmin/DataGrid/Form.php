<?php

namespace AtAdmin\DataGrid;
/**
 *
 */
class Form extends \Zend\Form
{
    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->setAction($_SERVER['REQUEST_URI'])
             ->setMethod('post')
             ->setEnctype('multipart/form-data')
             ->setAttrib('id', 'atf-grid-form')
             ->setAttrib('class', 'form-horizontal grid-form');
    }

    /**
     * @param $element
     * @param null $name
     * @param null $options
     */
    public function addElement($element, $name = null, $options = null)
    {
        if (!$element instanceof Zend_Form_Element) {
            $element = $this->createElement($element, $name, $options);
        }

        // default
        $element->setDecorators(array(
            array('ViewScript', array('viewScript' => 'grid/form/_element.tpl'))
        ));

        // checkbox
        // @todo: Research
        // Due to problems with send data then using viewScript decorator, use default decorators
        if ($element instanceof Zend_Form_Element_Checkbox) {
            $element->clearDecorators()
                    ->addDecorator('ViewHelper')
                    ->addDecorator('Errors')
                    ->addDecorator('Description', array('tag' => 'p', 'class' => 'help-block'))
                    ->addDecorator(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls'))
                    ->addDecorator('Label', array('class' => 'control-label'))
                    ->addDecorator(array('group' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group'));
        }

        // file
        if ($element instanceof Zend_Form_Element_File) {
            $element->setDecorators(
                array(
                    array('File'),
                    array('ViewScript', array('viewScript' => 'grid/form/_file-element.tpl', 'placement' => false))
                )
            );
        }

        // hash & hidden
        if ($element instanceof Zend_Form_Element_Hash || $element instanceof Zend_Form_Element_Hidden) {
            $element->clearDecorators()->addDecorators(array('viewHelper'));
        }

        parent::addElement($element, $name, $options);
    }
}