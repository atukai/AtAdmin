<?php

namespace AtAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AtAdmin\DataGrid;

class DataGridController extends AbstractActionController
{
    /**
     * @var \AtAdmin\DataGrid\DataGrid
     */
    protected $grid;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * @return void
     */
    public function listAction()
    {
        // Get grid object
    	$grid = $this->getGrid();

        if (!isset($_POST['cmd'])) {
            $requestParams = $this->getRequest()->getQuery();

            $filtersForm = $grid->getFiltersForm();
            $filtersForm->setData($requestParams);

            if ($filtersForm->isValid()) {
                $grid->applyFilters($filtersForm->getData());
            }

            $viewModel = new ViewModel(array('grid' => $grid));
	        $viewModel->setTemplate('at-admin/datagrid/grid');

            return $viewModel;
        } else {
            $this->_forward($_POST['cmd']);
        }
    }

    // CRUD

    /**
     * @throws \Exception
     */
    public function createAction()
    {
        $grid = $this->getGrid();

        if (!$grid->isAllowCreate()) {
            throw new \Exception('You are not allowed to do this.');
        }

        $requestParams = $this->getRequest()->getPost();

        $form = $grid->getForm();
        $form->setData($requestParams);

        if ($form->isValid()) {
            $formData = $this->preSave($form);

            echo '<pre>';var_dump($formData);exit;

            $itemId = $grid->save($formData);

            $this->postSave($grid, $itemId);

            //$this->_helper->flashMessenger->addMessage('Запись успешно добавлена');
            //$this->_helper->backToUrl();
            //$this->redirect('');
        }

        $viewModel = new ViewModel(array('grid' => $grid));
        $viewModel->setTemplate('at-admin/datagrid/create');

        return $viewModel;
    }

    /**
     * @throws ATF_Exception_NotAllowed
     */
    public function editAction()
    {
        $this->view->backUrl = $this->_helper->backToUrl->getBackUrl(false);

        /** @var ATF_DataGrid $grid */
        $grid = $this->_getGrid();

        if (!$grid->isAllowEdit()) {
            throw new ATF_Exception_NotAllowed();
        }

        $itemId = $this->_getParam('id');

        /** @var Zend_Form */
        $form = $grid->getForm();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $data = $this->preSave($form);

            $grid->save($data, $itemId);

            $this->postSave($grid, $itemId);

            $this->_helper->flashMessenger->addMessage('Данные записи #' . $itemId . ' успешно сохранены.');
            $this->_helper->backToUrl();
        }

        $item = $grid->getRow($itemId);
        $this->view->item = $item;

        $form->setDefaults($item->toArray());
        $this->view->form = $form;

        $this->view->grid = $grid;

        $currentPanel = $this->getRequest()->getParam('panel');
        $this->view->panel = $currentPanel;

        echo $this->renderScript('grid/edit.tpl');
    }

    /**
     * @throws ATF_Exception_NotAllowed
     */
    public function deleteAction()
    {
        $grid = $this->_getGrid();
        if (!$grid->isAllowDelete()) {
            throw new ATF_Exception_NotAllowed();
        }

        $itemId = $this->_getParam('id');
        $grid->delete($itemId);

        $this->_helper->flashMessenger->addMessage('Запись #' . $itemId . ' удалена.');

        // Back to previous page
        $this->_helper->backToUrl();
    }

    /**
     * Hook before save row
     * @todo: Use event here. See ZfcBase EventAwareForm
     *
     * @param $form
     * @return mixed
     */
    public function preSave($form)
    {
        $data = $form->getData();
        return $data;
    }

    /**
     * Hook after save row
     *
     * @param ATF_DataGrid $grid
     * @return mixed
     */
    public function postSave(ATF_DataGrid $grid, $primary)
    {
        return;
    }

    /**
     * @return \AtAdmin\DataGrid\DataGrid
     */
    public function getGrid()
    {
        return $this->grid;
    }
}