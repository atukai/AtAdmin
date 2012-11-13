<?php

namespace AtAdmin\DataGrid\Renderer;

/**
 * @todo Rename to ATF_DataGrid_Renderer_ZendView
 */
class Html extends AbstractRenderer
{
    /**
     * View object
     *
     * @var Zend_View_Abstract
     */
    protected $view = null;

    /**
     * Html template
     *
     * @var string
     */
    protected $template = 'at-admin/datagrid/grid/list';

    /**
     * Additional CSS rules
     *
     * @var string
     */
    protected $cssFile = '';

    /**
     * Set view object
     */
    public function setView(\Zend\View\Renderer\PhpRenderer $view)
    {
    	$this->view = $view;
    	return $this;
    }

    /**
     * @return Zend_View_Abstract|null
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param  $template
     * @return ATF_DataGrid_Renderer_Html
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param  $path
     * @return ATF_DataGrid_Renderer_Html
     */
    public function setCssFile($path)
    {
        $this->cssFile = $path;
        return $this;
    }

    /**
     * @param array $options
     * @return
     */
    public function render($variables = array())
    {
        $view = $this->getView();
        $viewModel = new \Zend\View\Model\ViewModel($variables);
        $viewModel->setTemplate($this->getTemplate());

        if (!empty($this->cssFile)) {
            //$this->getView()->headLink()->appendStylesheet($this->cssFile);
        }

        return $view->render($viewModel);
    }
}