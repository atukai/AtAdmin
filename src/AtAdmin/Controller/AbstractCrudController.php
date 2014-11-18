<?php

namespace AtAdmin\Controller;

use AtAdmin\Form\FormManager;
use AtDataGrid\DataGrid;
use AtDataGrid\Manager as GridManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

abstract class AbstractCrudController extends AbstractActionController
{
    /**
     * @var DataGrid
     */
    protected $grid;

    /**
     * @var GridManager
     */
    protected $gridManager;

    /**
     * @var FormManager
     */
    protected $formManager;

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * @return mixed
     */
    public function listAction()
    {
        // Save back url to redirect after actions
        $this->backTo()->setBackUrl();

        if (isset($_POST['cmd'])) {
            $this->_forward($_POST['cmd']);    // @todo refactor this
        }

        $grid = $this->getGrid();
        $grid->setOrder($this->request->getQuery('order', $grid->getIdentifierColumnName().'~desc'));

        if ($this->request->getQuery('page')) {
            $grid->setCurrentPage($this->request->getQuery('page'));
        }

        if ($this->request->getQuery('show_items')) {
            $grid->setItemsPerPage($this->request->getQuery('show_items'));
        }

        $gridManager = $this->getGridManager();

        $filtersForm = $gridManager->getFiltersForm();
        $filtersForm->setData($this->request->getQuery());
        if (!$filtersForm->isValid()) {
            //return $filtersForm->getMessages();
        }

        $grid->setFiltersData($filtersForm->getData());

        return $gridManager->render();
    }

    // CRUD

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function createAction()
    {
        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        if (! $gridManager->isAllowCreate()) {
            throw new \Exception('Creating is disabled');
        }

        $form = $this->getFormManager()->getForm($grid);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $grid->save($form->getData());
                $this->backTo()->previous('Record created');
            }
        }

        $viewModel = new ViewModel(array(
            'form' => $form,
            'tabs' => $this->getFormManager()->getFormTabs(),
            'backUrl' => $this->backTo()->getBackUrl(false),
        ));
        $viewModel->setTemplate('at-admin/create.phtml');

        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function editAction()
    {
        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        if (!$gridManager->isAllowEdit()) {
            throw new \Exception('Editing is disabled');
        }

        $itemId = $this->params('id');
        if (!$itemId) {
            throw new \Exception('Record not found');
        }

        $form = $this->getFormManager()->getForm($grid);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $grid->save($form->getData(), $itemId);
                $this->backTo()->previous('Record was updated');
            }
        }

        $item = $grid->getRow($itemId);
        $form->setData($item);

        $viewModel = new ViewModel(array(
            'item'        => $item,
            'form'        => $form,
            'backUrl'     => $this->backTo()->getBackUrl(false),
        ));
        $viewModel->setTemplate('at-admin/edit.phtml');

        return $viewModel;
    }

    /**
     * @throws \Exception
     */
    public function deleteAction()
    {
        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        if (!$gridManager->isAllowDelete()) {
            throw new \Exception('Deleting is disabled.');
        }

        $itemId = $this->params('id');
        if (!$itemId) {
            throw new \Exception('No record found.');
        }

        $grid->delete($itemId);
        $this->backTo()->previous('Record deleted.');
    }

    abstract public function getFormManager();

    abstract public function getGridManager();

    abstract public function getGrid();
}