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
    Zend\Stdlib\ResponseInterface,
    Zend\View\Model\ViewModel,
    CmsCommon\Stdlib\ArrayUtils;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
abstract class AbstractCrudController extends AbstractActionController
{
    const ACTION_CREATE = 'create';
    const ACTION_READ   = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_LIST   = 'list';

    /**
     * Event identifier
     *
     * @var string
     */
    protected $eventIdentifier = __CLASS__;

    /**
     * {@inheritDoc}
     */
    public function indexAction()
    {
        return $this->forward()->dispatch(
            $this->getEvent()->getRouteMatch()->getParam('controller'),
            ['action' => static::ACTION_LIST]
        );
    }

    /**
     * Retrieves list of persistence objects
     *
     * @return ViewModel
     */
    public function listAction($data)
    {
        return $this->forward()->dispatch(
            $this->getEvent()->getRouteMatch()->getParam('controller'),
            ['action' => 'notFound']
        );
    }

    /**
     * Creates new persistence object
     *
     * @param bool|array $data
     * @return ResponseInterface|ViewModel
     */
    public function createAction($data)
    {
        return $this->forward()->dispatch(
            $this->getEvent()->getRouteMatch()->getParam('controller'),
            ['action' => 'notFound']
        );
    }

    /**
     * Retrieves persistence object
     *
     * @param object $object
     * @return ResponseInterface|ViewModel
     */
    public function readAction($object)
    {
        return $this->forward()->dispatch(
            $this->getEvent()->getRouteMatch()->getParam('controller'),
            ['action' => 'notFound']
        );
    }

    /**
     * Updates persitence object
     *
     * @param bool|array $data
     * @param object $object
     * @return ResponseInterface|ViewModel
     */
    public function updateAction($data, $object)
    {
        return $this->forward()->dispatch(
            $this->getEvent()->getRouteMatch()->getParam('controller'),
            ['action' => 'notFound']
        );
    }

    /**
     * Removes persitence object
     *
     * @param object $object
     * @return ResponseInterface
     */
    public function deleteAction($object)
    {
        return $this->forward()->dispatch(
            $this->getEvent()->getRouteMatch()->getParam('controller'),
            ['action' => 'notFound']
        );
    }

    /**
     * @return ResponseInterface
     */
    protected function redirectToBaseRoute($params = [])
    {
        $params['controller'] = $this->getController();
        return $this->redirect()->toRoute($this->getBaseRoute(), $params);
    }

    /**
     * @return null|string
     */
    protected function getBaseRoute()
    {
        return $this->getOptions()->getBaseRoute() ?: null;
    }

    /**
     * @return string
     */
    protected function getController()
    {
        return $this->getEvent()->getRouteMatch()
            ->getParam(ModuleRouteListener::ORIGINAL_CONTROLLER);
    }

    /**
     * @param HttpRequest $files
     * @return bool
     */
    public static function hasUploadedFiles(HttpRequest $request)
    {
        $files = $request->getFiles()->toArray();
        return (bool) ArrayUtils::filterRecursive($files, function($value) {
            return $value && $value !== UPLOAD_ERR_NO_FILE;
        }, true);
    }
}
