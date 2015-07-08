<?php

namespace AtAdmin\Controller;

use AtDataGrid\DataGrid;
use AtDataGrid\Form\FormBuilder;
use AtDataGrid\Manager as GridManager;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;

abstract class AbstractAdminGridController extends AbstractAdminController
{
    const EVENT_CHECK_PERMISSIONS_LIST   = 'at-admin.check-permissions.list';
    const EVENT_CHECK_PERMISSIONS_CREATE = 'at-admin.check-permissions.create';
    const EVENT_CHECK_PERMISSIONS_EDIT   = 'at-admin.check-permissions.edit';
    const EVENT_CHECK_PERMISSIONS_DELETE = 'at-admin.check-permissions.delete';

    const EVENT_FORM_VALIDATION_PRE      = 'at-admin.form.validation.pre';
    const EVENT_SAVE_PRE                 = 'at-admin.save.pre';
    const EVENT_SAVE_POST                = 'at-admin.save.post';
    const EVENT_DELETE_PRE               = 'at-admin.delete.pre';
    const EVENT_DELETE_POST              = 'at-admin.delete.post';

    const EVENT_SET_TEMPLATE_CREATE      = 'at-admin.set-template.create';
    const EVENT_SET_TEMPLATE_EDIT        = 'at-admin.set-template.edit';

    const TITLE_ACTION_LIST              = '';
    const TITLE_ACTION_CREATE            = 'Create';
    const TITLE_ACTION_EDIT              = 'Edit';
    const TITLE_ACTION_DELETE            = 'Delete';

    /**
     * @var DataGrid
     */
    protected $grid;

    /**
     * @var GridManager
     */
    protected $gridManager;

    /**
     * @var FormBuilder
     */
    protected $formManager;

    public function indexAction()
    {
        return $this->notFoundAction();
    }

    /**
     * @return mixed
     */
    public function listAction()
    {
        $this->getEventManager()->trigger(self::EVENT_CHECK_PERMISSIONS_LIST, $this);

        // Save back url to redirect after actions
        $this->backTo()->setBackUrl();

        // Check for mass actions
        if (isset($_POST['cmd'])) {
            $this->forward($_POST['cmd']);    // @todo refactor this
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
            //$this->flashMessenger()->addMessage($filtersForm->getMessages());
        }

        $grid->setFiltersData($filtersForm->getData());

        return $gridManager->render();
    }

    /**
     * @return mixed|ViewModel
     * @throws \Exception
     */
    public function createAction()
    {
        $this->getEventManager()->trigger(self::EVENT_CHECK_PERMISSIONS_CREATE, $this);

        /** @var GridManager $gridManager */
        $gridManager = $this->getGridManager();

        /** @var FormBuilder $formManager */
        $formManager = $this->getFormManager();

        /** @var DataGrid $grid */
        $grid = $gridManager->getGrid();

        if (! $gridManager->isAllowCreate()) {
            throw new \Exception('Creating is disabled');
        }

        /** @var Form $form */
        $form = $formManager->build($grid);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            $this->getEventManager()->trigger(self::EVENT_FORM_VALIDATION_PRE, $form);

            if ($form->isValid()) {
                $data = $this->getRequest()->getPost();

                $this->getEventManager()->trigger(self::EVENT_SAVE_PRE, $this, $data);

                // Replace POST data with filtered and validated form values
                // POST data may contains not only form data
                $data = array_replace($data->toArray(), $form->getData());

                $id = $grid->save($data);

                $this->getEventManager()->trigger(self::EVENT_SAVE_POST, $grid->getRow($id), $data);

                return $this->backTo()->previous('Record created');
            } else {
                $this->flashMessenger()->addMessage('Check form data.');
            }
        }

        $viewModel = new ViewModel([
            'title'        => static::TITLE_ACTION_CREATE,
            'form'         => $form,
            'formSections' => $formManager->getFormSections(),
            'backUrl'      => $this->backTo()->getBackUrl(false),
        ]);

        $viewModel->setTemplate('at-admin/create.phtml');
        $eventResult = $this->getEventManager()->trigger(self::EVENT_SET_TEMPLATE_CREATE, $viewModel)->last();
        if ($eventResult) {
            $viewModel = $eventResult;
        }

        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function editAction()
    {
        $gridManager = $this->getGridManager();
        $formManager = $this->getFormManager();
        $eventManager = $this->getEventManager();

        /** @var DataGrid $grid */
        $grid = $gridManager->getGrid();

        if (!$gridManager->isAllowEdit()) {
            throw new \Exception('Updating is disabled');
        }

        $id = $this->params($grid->getIdentifierColumnName());
        if (!$id) {
            throw new \Exception('Record not found');
        }

        $item = $grid->getRow($id);
        if (!$item) {
            throw new \Exception('Record not found');
        }

        $eventManager->trigger(self::EVENT_CHECK_PERMISSIONS_EDIT, $item);

        /** @var Form $form */
        $form = $formManager->build($grid, FormBuilder::FORM_CONTEXT_EDIT, $item);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $form->setData($data);

            $eventManager->trigger(self::EVENT_FORM_VALIDATION_PRE, $form);

            if ($form->isValid()) {
                $eventManager->trigger(self::EVENT_SAVE_PRE, $item, $data);

                $grid->save($form->getData(), $id);

                // Replace POST data with filtered and validated form values
                $data = array_replace($data->toArray(), $form->getData());

                $eventManager->trigger(self::EVENT_SAVE_POST, $grid->getRow($id), $data);

                $this->backTo()->previous('Record was updated');
            } else {
                $this->flashMessenger()->addMessage('Check form data');
            }
        }

        $viewModel = new ViewModel([
            'title'        => static::TITLE_ACTION_EDIT,
            'item'         => $item,
            'form'         => $form,
            'formSections' => $this->getFormManager()->getFormSections(),
            'backUrl'      => $this->backTo()->getBackUrl(false),
        ]);

        $viewModel->setTemplate('at-admin/edit.phtml');
        $eventResult = $this->getEventManager()->trigger(self::EVENT_SET_TEMPLATE_EDIT, $viewModel, $item)->last();
        if ($eventResult) {
            $viewModel = $eventResult;
        }

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

        $id = $this->params('id');
        if (!$id) {
            return $this->notFoundAction();
        }

        $item = $grid->getRow($id);
        if (!$item) {
            return $this->notFoundAction();
        }

        $evm = $this->getEventManager();

        $evm->trigger(self::EVENT_CHECK_PERMISSIONS_DELETE, $item);

        $evm->trigger(self::EVENT_DELETE_PRE, $item);

        $grid->delete($id);

        $evm->trigger(self::EVENT_DELETE_POST, $item);

        $this->backTo()->previous('Record deleted.');
    }

    abstract public function getGrid();
    abstract public function getGridManager();
    abstract public function getFormManager();
}