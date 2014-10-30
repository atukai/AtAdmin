<?php

namespace AtAdmin\Controller;

use AtAdmin\Form\FormManager;
use AtDataGrid\Manager as GridManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

abstract class AbstractCrudController extends AbstractActionController
{
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

        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        if (isset($_POST['cmd'])) {
            $this->_forward($_POST['cmd']);    // @todo refactor this
        }

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
            'backUrl' => $this->backTo()->getBackUrl(false),
        ));
        $viewModel->setTemplate('at-datagrid/create');

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
        $viewModel->setTemplate('at-datagrid/edit');

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

    /**
     * @param FormManager $formManager
     */
    public function setFormManager(FormManager $formManager)
    {
        $this->formManager = $formManager;
    }

    /**
     * @return array|FormManager|object
     */
    public function getFormManager()
    {
        if (! $this->formManager) {
            $this->formManager = $this->getServiceLocator()->get('AtAdmin\Form\FormManager');
        }

        return $this->formManager;
    }

    /**
     * @param GridManager $gridManager
     */
    public function setGridManager(GridManager $gridManager)
    {
        $this->gridManager = $gridManager;
    }

    /**
     * @abstract
     * @return mixed
     */
    abstract public function getGridManager();
}