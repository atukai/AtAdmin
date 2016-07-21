<?php

namespace AtAdmin\Controller;

use AtDataGrid\DataGrid;
use AtDataGrid\Form\FormBuilder;
use AtDataGrid\Manager as GridManager;
use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;

class GridController extends AbstractAdminController
{
    // Events
    const EVENT_CHECK_PERMISSIONS_LIST   = 'at-admin.check-permissions.list';
    const EVENT_CHECK_PERMISSIONS_CREATE = 'at-admin.check-permissions.create';
    const EVENT_CHECK_PERMISSIONS_EDIT   = 'at-admin.check-permissions.edit';
    const EVENT_CHECK_PERMISSIONS_DELETE = 'at-admin.check-permissions.delete';

    const EVENT_VALIDATION_EDIT_PRE      = 'at-admin.validation.edit.pre';

    const EVENT_SAVE_PRE                 = 'at-admin.save.pre';
    const EVENT_SAVE_POST                = 'at-admin.save.post';
    const EVENT_DELETE_PRE               = 'at-admin.delete.pre';
    const EVENT_DELETE_POST              = 'at-admin.delete.post';

    const EVENT_SET_TEMPLATE_CREATE      = 'at-admin.set-template.create';
    const EVENT_SET_TEMPLATE_EDIT        = 'at-admin.set-template.edit';

    // Page titles
    // @todo Remove 
    const TITLE_ACTION_LIST              = '';
    const TITLE_ACTION_CREATE            = 'Create';
    const TITLE_ACTION_EDIT              = 'Edit';
    const TITLE_ACTION_DELETE            = 'Delete';

    /**
     * @var GridManager
     */
    protected $gridManager;

    /**
     * @return mixed
     */
    public function getAction()
    {
        $this->getEventManager()->trigger(self::EVENT_CHECK_PERMISSIONS_LIST, $this);

        // Save back url to redirect after actions
        $this->backTo()->setBackUrl();

        // Check for mass actions
        if (isset($_POST['cmd'])) {
            $this->forward($_POST['cmd']);    // @todo refactor this
        }

        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        if ($this->request->getQuery('order')) {
            $order = explode('~', $this->request->getQuery('order'));
            $grid->setOrder([$order[0] => $order[1]]);
        }

        if ($this->request->getQuery('page')) {
            $grid->setCurrentPage($this->request->getQuery('page'));
        }

        if ($this->request->getQuery('show_items')) {
            $grid->setItemsPerPage($this->request->getQuery('show_items'));
        }

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
        /** @var EventManager $eventManager */
        $eventManager = $this->getEventManager();
        $eventManager->trigger(self::EVENT_CHECK_PERMISSIONS_CREATE, $this);

        /** @var GridManager $gridManager */
        $gridManager = $this->getGridManager();
        if (! $gridManager->isAllowCreate()) {
            throw new \Exception('Creating is disabled');
        }

        /** @var FormBuilder $formManager */
        $formBuilder = $gridManager->getFormBuilder();

        /** @var DataGrid $grid */
        $grid = $gridManager->getGrid();

        /** @var Form $form */
        $form = $formBuilder->build($grid);

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
            'customJs'     => $formBuilder->getCustomJs(),
            'formSections' => $formBuilder->getFormSections(),
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
        /** @var Manager $gridManager */
        $gridManager = $this->getGridManager();
        if (!$gridManager->isAllowEdit()) {
            throw new \Exception('Editing is disabled');
        }

        /** @var DataGrid $grid */
        $grid = $gridManager->getGrid();

        $id = $this->params($grid->getIdentifierColumnName());
        if (!$id) {
            return $this->notFoundAction();
        }

        $item = $grid->getRow($id);
        if (!$item) {
            return $this->notFoundAction();
        }

        /** @var EventManager $eventManager */
        $eventManager = $this->getEventManager();
        $eventManager->trigger(self::EVENT_CHECK_PERMISSIONS_EDIT, $item);

        /** @var FormBuilder $formBuilder */
        $formBuilder = $gridManager->getFormBuilder();

        /** @var Form $form */
        $form = $formBuilder->build($grid, FormBuilder::FORM_CONTEXT_EDIT, $item);

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $form->setData($post);

            $this->getEventManager()->trigger(self::EVENT_VALIDATION_EDIT_PRE, $form, ['oldData' => $item, 'newData' => $post]);

            if ($form->isValid()) {
                // Replace POST data with filtered and validated form values
                // POST data may contains not only form data (extra data from sections)
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
            'customJs'     => $formBuilder->getCustomJs(),
            'formSections' => $formBuilder->getFormSections(),
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
        $evm->trigger(self::EVENT_CHECK_PERMISSIONS_DELETE, $item);

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

    /**
     * @param GridManager $manager
     */
    public function setGridManager(GridManager $manager)
    {
        $this->gridManager = $manager;
    }

    /**
     * @return GridManager
     */
    public function getGridManager()
    {
        if (!$this->gridManager) {
            throw new \RuntimeException('Grid manager was not set');
        }

        return $this->gridManager;
    }
}