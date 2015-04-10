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

use Zend\Http\Request as HttpRequest,
    Zend\Mvc\Controller\AbstractActionController,
    Zend\Mvc\ModuleRouteListener,
    Zend\Mvc\MvcEvent,
    Zend\Stdlib\ResponseInterface,
    Zend\View\Model\ViewModel,
    CmsCommon\Service\DomainServiceProviderInterface,
    CmsCommon\Service\DomainServiceProviderTrait,
    CmsCommon\Stdlib\OptionsProviderInterface,
    CmsCommon\Stdlib\OptionsProviderTrait;

/**
 * @property CrudControllerOptionsInterface $options
 * @method CrudControllerOptionsInterface getOptions()
 * @method self setOptions(CrudControllerOptionsInterface $options)
 */
abstract class AbstractCrudController extends AbstractActionController implements
        DomainServiceProviderInterface,
        OptionsProviderInterface
{
    use DomainServiceProviderTrait,
        OptionsProviderTrait;

    /**
     * Event identifier
     *
     * @var string
     */
    protected $eventIdentifier = __CLASS__;

    /**
     * {@inheritDoc}
     */
    public function onDispatch(MvcEvent $e)
    {
        $request = $e->getRequest();

        if (!$request instanceof HttpRequest) {
            throw new \RuntimeException('CRUD controller from CmsCommon can only handle HTTP requests');
        }

        $url = $this->url()->fromRoute();
        $prg = $this->prg($url, true);
        // Return early if prg plugin returned a response
        if ($prg instanceof ResponseInterface) {
            return $prg;
        }

        $data = $prg;

        $action = $this->params()->fromRoute('action', 'not-found');
        $method = static::getMethodFromAction($action);

        if (!method_exists($this, $method)) {
            $method = 'notFoundAction';
        }

        if ($action === 'read' || $action === 'update' || $action === 'delete') {
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

        switch ($action) {
            case 'create':
                $result = $this->$method($data);
                break;
            case 'read':
                $result = $this->$method($object);
                break;
            case 'update':
                $result = $this->$method($object, $data);
                break;
            case 'delete':
                $result = $this->$method($object);
                break;
            default:
                $result = $this->$method($data);
        }

        $e->setResult($result);
    }

    /**
     * {@inheritDoc}
     */
    public function indexAction()
    {
        return $this->forward()->dispatch(
            $this->getEvent()->getRouteMatch()->getParam('controller'),
            ['action' => 'list']
        );
    }

    /**
     * Retrieves list of persistence objects
     *
     * @return ViewModel
     */
    public function listAction()
    {
        $options = $this->getOptions();
        $service = $this->getDomainService();

        $form = $service->getForm();
        $paginator = $service->getMapper()->getPaginator();

        $viewModel = new ViewModel(compact('form', 'options', 'paginator'));
        $viewModel->setTemplate($options->getListTemplate());

        $params = compact('form', 'options', 'paginator', 'service', 'viewModel');
        $this->getEventManager()->trigger('list', $this, $params);

        return $viewModel;
    }

    /**
     * Creates new persistence object
     *
     * @return ResponseInterface|ViewModel
     */
    public function createAction($data)
    {
        $options = $this->getOptions();
        $service = $this->getDomainService();

        $form = $service->getForm();
        $form->setAttribute('action', $this->url()->fromRoute());

        $viewModel = new ViewModel(compact('form', 'options'));
        $viewModel->setTemplate($options->getCreateTemplate());

        if ($data && $form->setData($data)->isValid()) {
            $object = $form->getObject();
            $params = compact('form', 'object', 'options', 'service', 'viewModel');

            $fm = $this->flashMessenger();

            try {
                $this->getEventManager()->trigger('create', $this, $params);
                $service->getMapper()->add($object)->save();
            } catch (\Exception $e) {
                $fm->setNamespace($form->getName() . '-' . $fm::NAMESPACE_ERROR)
                   ->addMessage($e->getMessage());

                return $viewModel;
            }

            $fm->setNamespace($form->getName() . '-' . $fm::NAMESPACE_SUCCESS)
               ->addMessage($this->translate('An object has been successfully created'));
        }

        return $viewModel;
    }

    /**
     * Retrieves persistence object
     *
     * @return ResponseInterface|ViewModel
     */
    public function readAction($object)
    {
        $options = $this->getOptions();
        $service = $this->getDomainService();

        $form = $service->getForm();
        $form->setAttribute('action', $this->url()->fromRoute());
        $form->bind($object);

        $viewModel = new ViewModel(compact('form', 'options'));
        $viewModel->setTemplate($options->getReadTemplate());

        $params = compact('form', 'object', 'options', 'service', 'viewModel');
        $this->getEventManager()->trigger('read', $this, $params);

        return $viewModel;
    }

    /**
     * Updates persitence object
     *
     * @return ResponseInterface|ViewModel
     */
    public function updateAction($object, $data)
    {
        $options = $this->getOptions();
        $service = $this->getDomainService();

        $form = $service->getForm();
        $form->setAttribute('action', $this->url()->fromRoute());
        $form->bind($object);

        $viewModel = new ViewModel(compact('form', 'options'));
        $viewModel->setTemplate($options->getUpdateTemplate());

        if ($data && $form->setData($data)->isValid()) {
            $object = $form->getObject();
            $params = compact('form', 'object', 'options', 'service', 'viewModel');

            $fm = $this->flashMessenger();

            try {
                $this->getEventManager()->trigger('update', $this, $params);
                $service->getMapper()->add($object)->save();
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
     * Removes persitence object
     *
     * @return ResponseInterface
     */
    public function deleteAction($object)
    {
        $options   = $this->getOptions();
        $viewModel = new ViewModel(compact('object', 'options'));

        if ($options->getUseDeleteConfirmation() && !$this->params()->fromRoute($options->getDeleteConfirmKey())) {
            $viewModel->setTemplate($options->getDeleteTemplate());
            return $viewModel;
        }

        $service = $this->getDomainService();
        $params  = compact('object', 'options', 'service', 'viewModel');

        $fm = $this->flashMessenger();

        try {
            $this->getEventManager()->trigger('delete', $this, $params);
            $service->getMapper()->remove($object)->save();
        } catch(\Exception $e) {
            $fm->addErrorMessage($e->getMessage());
            return $this->redirectToBaseRoute();
        }

        $fm->addSuccessMessage($this->translate('An object has been successfully removed'));
        return $this->redirectToBaseRoute();
    }

    /**
     * @return ResponseInterface
     */
    protected function redirectToBaseRoute()
    {
        if ($baseRoute = $this->getOptions()->getBaseRoute()) {
            return $this->redirect()->toRoute($baseRoute);
        }

        $routeMatch = $this->getEvent()->getRouteMatch();
        $controller = $routeMatch->getParam(ModuleRouteListener::ORIGINAL_CONTROLLER);

        return $this->redirect()->toRoute($baseRoute, compact('controller'));
    }
}
