<?php

namespace AtAdmin\Controller;

use AtDataGrid\DataGrid;
use AtDataGrid\Form\FormBuilder;
use AtDataGrid\Manager as GridManager;
use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\ViewModel;

abstract class AbstractAdminGridController extends AbstractAdminController
{
    // Events
    const EVENT_CHECK_PERMISSIONS_LIST   = 'at-admin.check-permissions.list';
    const EVENT_CHECK_PERMISSIONS_CREATE = 'at-admin.check-permissions.create';
    const EVENT_CHECK_PERMISSIONS_EDIT   = 'at-admin.check-permissions.edit';

    const EVENT_SAVE_PRE                 = 'at-admin.save.pre';
    const EVENT_SAVE_POST                = 'at-admin.save.post';
    const EVENT_DELETE_PRE               = 'at-admin.delete.pre';
    const EVENT_DELETE_POST              = 'at-admin.delete.post';

    const EVENT_SET_TEMPLATE_CREATE      = 'at-admin.set-template.create';
    const EVENT_SET_TEMPLATE_EDIT        = 'at-admin.set-template.edit';

    // Page titles
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

    /**
     * @return array
     */
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

        /** @var EventManager $eventManager */
        $eventManager = $this->getEventManager();

        if (! $gridManager->isAllowCreate()) {
            throw new \Exception('Creating is disabled');
        }

        /** @var FormBuilder $formManager */
        $formManager = $this->getFormManager();

        /** @var DataGrid $grid */
        $grid = $gridManager->getGrid();

        /** @var Form $form */
        $form = $formManager->build($grid);

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $form->setData($post);

            if ($form->isValid()) {
                // Replace POST data with filtered and validated form values
                // POST data may contains not only form data
                $data = array_replace($post->toArray(), $form->getData());

                $eventManager->trigger(self::EVENT_SAVE_PRE, null, $data);
                $item = $grid->save($data);
                $eventManager->trigger(self::EVENT_SAVE_POST, $item, $data);

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
        $eventResult = $eventManager->trigger(self::EVENT_SET_TEMPLATE_CREATE, $viewModel)->last();
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
            throw new \Exception('Editing is disabled');
        }

        $id = $this->params($grid->getIdentifierColumnName());
        if (!$id) {
            return $this->notFoundAction();
        }

        $item = $grid->getRow($id);
        if (!$item) {
            return $this->notFoundAction();
        }

        $eventManager->trigger(self::EVENT_CHECK_PERMISSIONS_EDIT, $item);

        /** @var Form $form */
        $form = $formManager->build($grid, FormBuilder::FORM_CONTEXT_EDIT, $item);

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $form->setData($post);

            if ($form->isValid()) {
                // Replace POST data with filtered and validated form values
                // POST data may contains not only form data
                $data = array_replace($post->toArray(), $form->getData());

                $eventManager->trigger(self::EVENT_SAVE_PRE, $item, $data);
                $item = $grid->save($data, $id);
                $eventManager->trigger(self::EVENT_SAVE_POST, $item, $data);

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
        $eventResult = $this->getEventManager()->trigger(self::EVENT_SET_TEMPLATE_EDIT, $viewModel, ['item' => $item])->last();
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
        if (!$gridManager->isAllowDelete()) {
            throw new \Exception('Deleting is disabled.');
        }

        $id = $this->params('id');
        if (!$id) {
            return $this->notFoundAction();
        }

        $grid = $gridManager->getGrid();
        $item = $grid->getRow($id);
        if (!$item) {
            return $this->notFoundAction();
        }

        $evm = $this->getEventManager();
        // Get additional params
        $params = array_merge_recursive(
            $this->params()->fromQuery(),
            $this->params()->fromPost()
        );

        $evm->trigger(self::EVENT_DELETE_PRE, $item, $params);
        $grid->delete($id);
        $evm->trigger(self::EVENT_DELETE_POST, $item, $params);

        $this->backTo()->previous('Record deleted.');
    }

    abstract public function getGrid();
    abstract public function getGridManager();
    abstract public function getFormManager();
}