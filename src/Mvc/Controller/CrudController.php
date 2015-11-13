<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mvc\Controller;

use RuntimeException,
    Zend\Http\Request as HttpRequest,
    Zend\Mvc\MvcEvent,
    Zend\Stdlib\Parameters,
    Zend\Stdlib\ResponseInterface,
    Zend\View\Model\ViewModel,
    CmsCommon\Form\FormInterface,
    CmsCommon\Mapping\Common\IdentifiableInterface,
    CmsCommon\Service\DomainServiceInterface,
    CmsCommon\Service\DomainServiceProviderInterface,
    CmsCommon\Service\DomainServiceProviderTrait,
    CmsCommon\Mvc\Controller\Options\CrudControllerOptionsInterface,
    CmsCommon\Stdlib\OptionsProviderInterface,
    CmsCommon\Stdlib\OptionsProviderTrait,
    CmsDatagrid\Column\Select;
use CmsDatagrid\Column\Formatter\GenerateLink;
use CmsDatagrid\Column\Action;
use CmsDatagrid\Column\Action\Button;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 *
 * @property CrudControllerOptionsInterface $options
 * @method   CrudControllerOptionsInterface getOptions()
 * @method   self setOptions(CrudControllerOptionsInterface $options)
 */
class CrudController extends AbstractCrudController implements
        DomainServiceProviderInterface,
        OptionsProviderInterface
{
    use DomainServiceProviderTrait,
        OptionsProviderTrait;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * __construct
     *
     * @param DomainServiceInterface $service
     * @param CrudControllerOptionsInterface $options
     */
    public function __construct(
        DomainServiceInterface $service,
        CrudControllerOptionsInterface $options
    ) {
        $this->setDomainService($service);
        $this->setOptions($options);
    }

    /**
     * {@inheritDoc}
     *
     * @throws RuntimeException
     */
    public function onDispatch(MvcEvent $e)
    {
        $request = $e->getRequest();
        if (!$request instanceof HttpRequest) {
            throw new RuntimeException('CRUD controller from CmsCommon can only handle HTTP requests');
        }

        $action = $this->params()->fromRoute('action', 'not-found');
        $method = static::getMethodFromAction($action);

        if (!method_exists($this, $method)) {
            $method = 'notFoundAction';
        }

        $object = null;
        if ($action === static::ACTION_READ ||
            $action === static::ACTION_UPDATE ||
            $action === static::ACTION_DELETE
        ) {
            $options = $this->getOptions();
            $id = $this->params()->fromRoute($options->getIdentifierKey());

            if (null === $id) {
                $this->flashMessenger()->addWarningMessage(sprintf(
                    $this->translate('An identifier must be provided to %s an object'),
                    $this->translate($action)
                ));

                return $this->redirectToBaseRoute();
            }

            $object = $this->getDomainService()->getMapper()->find($id);
            if (!$object) {
                $this->flashMessenger()->addWarningMessage($this->translate('An object cannot be found'));
                return $this->redirectToBaseRoute();
            }
        }

        $data = $this->getData($object);

        // Return early if getData method returned a response
        if ($data instanceof ResponseInterface) {
            $actionResponse = $data;
        } else {
            switch ($action) {
                case static::ACTION_READ:
                case static::ACTION_DELETE:
                    $actionResponse = $this->$method($object);
                    break;
                case static::ACTION_UPDATE:
                    $actionResponse = $this->$method($data, $object);
                    break;
                default:
                    $actionResponse = $this->$method($data);
            }
        }

        $e->setResult($actionResponse);
    }

    /**
     * {@inheritDoc}
     */
    public function listAction($data)
    {
        $form       = $this->getForm();
        $options    = $this->getOptions();
        $service    = $this->getDomainService();

        if ($data) {
            $this->getRequest()->setPost(new Parameters($data));
        }

        /* @var $datagrid \CmsDatagrid\Datagrid */
        $datagrid   = $this->getServiceLocator()->get('CmsDatagrid');
        $datagrid->setDataSource($service->getMapper());

        $col = new Select('id');
        $col->setIdentity(true);
        $datagrid->addColumn($col);

        $col = new Select('name');
        $col->setLabel('Name');
        $col->setWidth(85);
        $datagrid->addColumn($col);

        $updateAction = new Button();
        $updateAction->setLabel('Update');
        $updateAction->addClass('btn-primary');
        $rowId = $updateAction->getRowIdPlaceholder();
        $updateAction->setLink('update/' . $rowId);

        $col = new Action();
        $col->setLabel('');
        $col->setWidth(15);
        $col->addAction($updateAction);
        
        $datagrid->addColumn($col);

        /*$col->addFormatter(new GenerateLink(
            $this->getServiceLocator(),
            $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'id',
            ['controller' => 'hunting-farm', 'action' => 'update']
        ));*/

        

        $datagrid->render();

        return $datagrid->getResponse();

        $viewModel = new ViewModel(compact('form', 'options', 'service'));
        $viewModel->setTemplate($options->getListTemplate());

        $params = compact('form', 'options', 'service', 'viewModel');
        $this->getEventManager()->trigger(static::ACTION_LIST, $this, $params);

        return $viewModel;
    }

    /**
     * {@inheritDoc}
     */
    public function createAction($data)
    {
        $options = $this->getOptions();
        $form    = $this->getForm();

        $viewModel = new ViewModel(compact('form', 'options'));
        $viewModel->setTemplate($options->getCreateTemplate());

        if ($data && $form->isValid()) {
            $object = $form->getObject();
            $params = compact('form', 'object', 'options', 'service', 'viewModel');

            $fm = $this->flashMessenger();

            try {
                $this->getEventManager()->trigger(static::ACTION_CREATE, $this, $params);
                $this->getDomainService()->getMapper()->add($object)->save($object);
                $form->bind($object);
            } catch (\Exception $e) {
                $fm->setNamespace($form->getName() . '-' . $fm::NAMESPACE_ERROR)
                   ->addMessage($e->getMessage());

                return $viewModel;
            }

            if ($object instanceof IdentifiableInterface) {
                $form->bind($object);

                $params = [
                    'action' => static::ACTION_UPDATE,
                    $this->getOptions()->getIdentifierKey() => $object->getId()
                ];

                $id = $this->url()->fromRoute(null, $params, true);
                $form->setAttribute('action', $id);
            }

            $fm->setNamespace($form->getName() . '-' . $fm::NAMESPACE_SUCCESS)
               ->addMessage($this->translate('An object has been successfully created'));
        }

        return $viewModel;
    }

    /**
     * {@inheritDoc}
     */
    public function readAction($object)
    {
        $options = $this->getOptions();
        $form    = $this->getForm();

        $viewModel = new ViewModel(compact('form', 'options'));
        $viewModel->setTemplate($options->getReadTemplate());

        $params = compact('form', 'object', 'options', 'service', 'viewModel');
        $this->getEventManager()->trigger(static::ACTION_READ, $this, $params);

        return $viewModel;
    }

    /**
     * {@inheritDoc}
     */
    public function updateAction($data, $object)
    {
        $options = $this->getOptions();
        $form    = $this->getForm();

        $viewModel = new ViewModel(compact('form', 'options'));
        $viewModel->setTemplate($options->getUpdateTemplate());

        if ($data && $form->isValid()) {
            $object = $form->getData();
            $params = compact('form', 'object', 'options', 'service', 'viewModel');

            $fm = $this->flashMessenger();

            try {
                $this->getEventManager()->trigger(static::ACTION_UPDATE, $this, $params);
                $this->getDomainService()->getMapper()->add($object)->save($object);
                $form->bind($object);
            } catch (\Exception $e) {
                $fm->setNamespace($form->getName() . '-' . $fm::NAMESPACE_ERROR)
                   ->addMessage($e->getMessage());

                return $viewModel;
            }

            $fm->setNamespace($form->getName() . '-' . $fm::NAMESPACE_SUCCESS)
               ->addMessage($this->translate('An object has been successfully updated'));
        }

        return $viewModel;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAction($object)
    {
        $options = $this->getOptions();
        $viewModel = new ViewModel(compact('object', 'options'));

        if ($options->getUseDeleteConfirmation() && !$this->params()->fromRoute($options->getDeleteConfirmKey())) {
            $viewModel->setTemplate($options->getDeleteTemplate());
            return $viewModel;
        }

        $service = $this->getDomainService();
        $params = compact('object', 'options', 'service', 'viewModel');

        $fm = $this->flashMessenger();

        try {
            $this->getEventManager()->trigger(static::ACTION_DELETE, $this, $params);
            $service->getMapper()->remove($object)->save($object);
        } catch(\Exception $e) {
            $fm->addErrorMessage($e->getMessage());
            return $this->redirectToBaseRoute();
        }

        $fm->addSuccessMessage($this->translate('An object has been successfully removed'));
        return $this->redirectToBaseRoute();
    }

    /**
     * @param string        $action action attribute
     * @param bool|array    $data   form data to be set
     * @param object        $object binding object
     * @return null|FormInterface
     */
    protected function getForm($action = null, $data = null, $object = null)
    {
        if (!$this->form && !($this->form = $this->getDomainService()->getForm())) {
            return;
        }

        if ($action) {
            $this->form->setAttribute('action', $action);
        }

        if (is_object($object)) {
            $this->form->bind($object);
        }

        if (is_array($data)) {
            $this->form->setData($data);
        }

        return $this->form;
    }

    /**
     * @param object $object can be null for newly created object
     * @return bool|array|ResponseInterface
     */
    private function getData($object)
    {
        $url = $this->url()->fromRoute(null, [], true);

        $request = $this->getRequest();
        if ($request->isPost()) {
            return static::hasUploadedFiles($request) && ($form = $this->getForm($url, null, $object))
                ? $this->fileprg($form, $url, true)
                : $this->prg($url, true);
        }

        $prg = $this->prg($url, true);
        if ($prg === false) {
            if ($form = $this->getForm($url, null, $object)) {
                $prg = $this->fileprg($form, $url, true);
            }
        } else {
            $form = $this->getForm($url, $prg, $object);
        }

        return $prg;
    }
}
