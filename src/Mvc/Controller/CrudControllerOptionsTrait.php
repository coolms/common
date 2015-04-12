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

trait CrudControllerOptionsTrait
{
    /**
     * @var bool
     */
    protected $useDeleteConfirmation = true;

    /**
     * @var string
     */
    protected $deleteConfirmKey = 'confirm';

    /**
     * @var string
     */
    protected $createRoute;

    /**
     * @var string
     */
    protected $readRoute;

    /**
     * @var string
     */
    protected $updateRoute;

    /**
     * @var string
     */
    protected $deleteRoute;

    /**
     * @var string
     */
    protected $listRoute;

    /**
     * @var string
     */
    protected $createSuffix = 'create';

    /**
     * @var string
     */
    protected $readSuffix = 'read';

    /**
     * @var string
     */
    protected $updateSuffix = 'update';

    /**
     * @var string
     */
    protected $deleteSuffix = 'delete';

    /**
     * @var string
     */
    protected $listSuffix = 'list';

    /**
     * @var string
     */
    protected $formSuffix = 'form';

    /**
     * @var string
     */
    protected $templatePrefix = 'cms-common/crud';

    /**
     * @var string
     */
    protected $createTemplate;

    /**
     * @var string
     */
    protected $readTemplate;

    /**
     * @var string
     */
    protected $updateTemplate;

    /**
     * @var string
     */
    protected $deleteTemplate;

    /**
     * @var string
     */
    protected $listTemplate;

    /**
     * @var string
     */
    protected $formTemplate;

    /**
     * Sets request identifier param name
     *
     * @param string $key
     * @return self
     */
    abstract public function setIdentifierKey($key);

    /**
     * Retrieves request identifier param name
     *
     * @return string
     */
    abstract public function getIdentifierKey();

    /**
     * Sets use delete confirmation
     *
     * @param bool $flag
     * @return self
     */
    public function setUseDeleteConfirmation($flag)
    {
        $this->useDeleteConfirmation = $flag;

        return $this;
    }

    /**
     * Gets use delete confirmation
     *
     * @return bool
     */
    public function getUseDeleteConfirmation()
    {
        return $this->useDeleteConfirmation;
    }

    /**
     * Sets delete confirm key
     *
     * @param string $key
     * @return self
     */
    public function setDeleteConfirmKey($key)
    {
        $this->deleteConfirmKey = $key;

        return $this;
    }

    /**
     * Retrieves delete confirm key
     *
     * @return string
     */
    public function getDeleteConfirmKey()
    {
        return $this->deleteConfirmKey;
    }

    /**
     * Sets base route
     *
     * @param string $route
     * @return self
     */
    abstract public function setBaseRoute($route);

    /**
     * Retrieves base route
     *
     * @return string
     */
    abstract public function getBaseRoute();

    /**
     * Sets create route
     *
     * @param string $route
     * @return self
     */
    public function setCreateRoute($route)
    {
        $this->createRoute = $route;

        return $this;
    }

    /**
     * Retrieves create route
     *
     * @return string
     */
    public function getCreateRoute()
    {
        if (null === $this->createRoute) {
            $route = rtrim($this->getBaseRoute(), '/') . '/' . $this->getCreateSuffix();
            $this->setCreateRoute($route);
        }

        return $this->createRoute;
    }

    /**
     * Sets read route
     *
     * @param string $route
     * @return self
     */
    public function setReadRoute($route)
    {
        $this->readRoute = $route;

        return $this;
    }

    /**
     * Retrieves read route
     *
     * @return string
     */
    public function getReadRoute()
    {
        if (null === $this->readRoute) {
            $route = rtrim($this->getBaseRoute(), '/') . '/' . $this->getReadSuffix();
            $this->setReadRoute($route);
        }

        return $this->readRoute;
    }

    /**
     * Sets update route
     *
     * @param string $route
     * @return self
     */
    public function setUpdateRoute($route)
    {
        $this->updateRoute = $route;

        return $this;
    }

    /**
     * Retrieves update route
     *
     * @return string
     */
    public function getUpdateRoute()
    {
        if (null === $this->updateRoute) {
            $route = rtrim($this->getBaseRoute(), '/') . '/' . $this->getUpdateSuffix();
            $this->setUpdateRoute($route);
        }

        return $this->updateRoute;
    }

    /**
     * Sets delete route
     *
     * @param string $route
     * @return self
     */
    public function setDeleteRoute($route)
    {
        $this->deleteRoute = $route;

        return $this;
    }
    
    /**
     * Retrieves delete route
     *
     * @return string
     */
    public function getDeleteRoute()
    {
        if (null === $this->deleteRoute) {
            $route = rtrim($this->getBaseRoute(), '/') . '/' . $this->getDeleteSuffix();
            $this->setDeleteRoute($route);
        }

        return $this->deleteRoute;
    }

    /**
     * Sets list route
     *
     * @param string $route
     * @return self
     */
    public function setListRoute($route)
    {
        $this->listRoute = $route;

        return $this;
    }

    /**
     * Retrieves list route
     *
     * @return string
     */
    public function getListRoute()
    {
        if (null === $this->listRoute) {
            $route = rtrim($this->getBaseRoute(), '/') . '/' . $this->getListSuffix();
            $this->setListRoute($route);
        }

        return $this->listRoute;
    }

    /**
     * Sets create suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setCreateSuffix($suffix)
    {
        $this->createSuffix = $suffix;

        return $this;
    }

    /**
     * Retrieves create suffix
     *
     * @return string
     */
    public function getCreateSuffix()
    {
        return $this->createSuffix;
    }

    /**
     * Sets read suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setReadSuffix($suffix)
    {
        $this->readSuffix = $suffix;

        return $this;
    }

    /**
     * Retrieves read suffix
     *
     * @return string
     */
    public function getReadSuffix()
    {
        return $this->readSuffix;
    }

    /**
     * Sets update suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setUpdateSuffix($suffix)
    {
        $this->updateSuffix = $suffix;

        return $this;
    }

    /**
     * Retrieves update suffix
     *
     * @return string
     */
    public function getUpdateSuffix()
    {
        return $this->updateSuffix;
    }

    /**
     * Sets delete suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setDeleteSuffix($suffix)
    {
        $this->deleteSuffix = $suffix;

        return $this;
    }

    /**
     * Retrieves delete suffix
     *
     * @return string
     */
    public function getDeleteSuffix()
    {
        return $this->deleteSuffix;
    }

    /**
     * Sets list suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setListSuffix($suffix)
    {
        $this->listSuffix = $suffix;

        return $this;
    }

    /**
     * Retrieves list suffix
     *
     * @return string
     */
    public function getListSuffix()
    {
        return $this->listSuffix;
    }

    /**
     * Sets form suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setFormSuffix($suffix)
    {
        $this->formSuffix = $suffix;

        return $this;
    }

    /**
     * Retrieves form suffix
     *
     * @return string
     */
    public function getFormSuffix()
    {
        return $this->formSuffix;
    }

    /**
     * Sets template prefix
     *
     * @param string $prefix
     * @return self
     */
    public function setTemplatePrefix($prefix)
    {
        $this->templatePrefix = $prefix;

        return $this;
    }

    /**
     * Retrieves template prefix
     *
     * @return string
     */
    public function getTemplatePrefix()
    {
        return $this->templatePrefix;
    }

    /**
     * Sets create template
     *
     * @param string $template
     * @return self
     */
    public function setCreateTemplate($template)
    {
        $this->createTemplate = $template;

        return $this;
    }

    /**
     * Retrieves create template
     *
     * @return string
     */
    public function getCreateTemplate()
    {
        if (null === $this->createTemplate) {
            $template = rtrim($this->getTemplatePrefix(), '/') . '/' . $this->getCreateSuffix();
            $this->setCreateTemplate($template);
        }

        return $this->createTemplate;
    }

    /**
     * Sets read template
     *
     * @param string $template
     * @return self
     */
    public function setReadTemplate($template)
    {
        $this->readTemplate = $template;

        return $this;
    }

    /**
     * Retrieves read template
     *
     * @return string
     */
    public function getReadTemplate()
    {
        if (null === $this->readTemplate) {
            $template = rtrim($this->getTemplatePrefix(), '/') . '/' . $this->getReadSuffix();
            $this->setReadTemplate($template);
        }

        return $this->readTemplate;
    }

    /**
     * Sets update template
     *
     * @param string $template
     * @return self
     */
    public function setUpdateTemplate($template)
    {
        $this->updateTemplate = $template;

        return $this;
    }

    /**
     * Retrieves update template
     *
     * @return string
     */
    public function getUpdateTemplate()
    {
        if (null === $this->updateTemplate) {
            $template = rtrim($this->getTemplatePrefix(), '/') . '/' . $this->getUpdateSuffix();
            $this->setUpdateTemplate($template);
        }

        return $this->updateTemplate;
    }

    /**
     * Sets delete template
     *
     * @param string $template
     * @return self
     */
    public function setDeleteTemplate($template)
    {
        $this->deleteTemplate = $template;

        return $this;
    }

    /**
     * Retrieves delete template
     *
     * @return string
     */
    public function getDeleteTemplate()
    {
        if (null === $this->deleteTemplate) {
            $template = rtrim($this->getTemplatePrefix(), '/') . '/' . $this->getDeleteSuffix();
            $this->setDeleteTemplate($template);
        }

        return $this->deleteTemplate;
    }

    /**
     * Sets list template
     *
     * @param string $template
     * @return self
     */
    public function setListTemplate($template)
    {
        $this->listTemplate = $template;

        return $this;
    }

    /**
     * Retrieves list template
     *
     * @return string
     */
    public function getListTemplate()
    {
        if (null === $this->listTemplate) {
            $template = rtrim($this->getTemplatePrefix(), '/') . '/' . $this->getListSuffix();
            $this->setListTemplate($template);
        }

        return $this->listTemplate;
    }

    /**
     * Sets form template
     *
     * @param string $template
     * @return self
     */
    public function setFormTemplate($template)
    {
        $this->formTemplate = $template;

        return $this;
    }

    /**
     * Retrieves form template
     *
     * @return string
     */
    public function getFormTemplate()
    {
        if (null === $this->formTemplate) {
            $template = rtrim($this->getTemplatePrefix(), '/') . '/' . $this->getFormSuffix();
            $this->setFormTemplate($template);
        }

        return $this->formTemplate;
    }
}
