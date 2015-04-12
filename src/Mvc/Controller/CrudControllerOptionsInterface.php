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

interface CrudControllerOptionsInterface extends ControllerOptionsInterface
{
    /**
     * Sets use delete confirmation
     *
     * @param bool $flag
     * @return self
     */
    public function setUseDeleteConfirmation($flag);

    /**
     * Gets use delete confirmation
     *
     * @return bool
     */
    public function getUseDeleteConfirmation();

    /**
     * Sets delete confirm key
     *
     * @param string $key
     * @return self
     */
    public function setDeleteConfirmKey($key);

    /**
     * Retrieves delete confirm key
     *
     * @return string
     */
    public function getDeleteConfirmKey();

    /**
     * Sets create route
     *
     * @param string $route
     * @return self
     */
    public function setCreateRoute($route);

    /**
     * Retrieves create route
     *
     * @return string
     */
    public function getCreateRoute();

    /**
     * Sets read route
     *
     * @param string $route
     * @return self
     */
    public function setReadRoute($route);

    /**
     * Retrieves read route
     *
     * @return string
     */
    public function getReadRoute();

    /**
     * Sets update route
     *
     * @param string $route
     * @return self
     */
    public function setUpdateRoute($route);

    /**
     * Retrieves update route
     *
     * @return string
     */
    public function getUpdateRoute();

    /**
     * Sets delete route
     *
     * @param string $route
     * @return self
     */
    public function setDeleteRoute($route);

    /**
     * Retrieves delete route
     *
     * @return string
     */
    public function getDeleteRoute();

    /**
     * Sets list route
     *
     * @param string $route
     * @return self
     */
    public function setListRoute($route);

    /**
     * Retrieves list route
     *
     * @return string
     */
    public function getListRoute();

    /**
     * Sets create suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setCreateSuffix($suffix);

    /**
     * Retrieves create suffix
     *
     * @return string
     */
    public function getCreateSuffix();

    /**
     * Sets read suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setReadSuffix($suffix);

    /**
     * Retrieves read suffix
     *
     * @return string
     */
    public function getReadSuffix();

    /**
     * Sets update suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setUpdateSuffix($suffix);

    /**
     * Retrieves update suffix
     *
     * @return string
     */
    public function getUpdateSuffix();

    /**
     * Sets delete suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setDeleteSuffix($suffix);

    /**
     * Retrieves delete suffix
     *
     * @return string
     */
    public function getDeleteSuffix();

    /**
     * Sets list suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setListSuffix($suffix);

    /**
     * Retrieves list suffix
     *
     * @return string
     */
    public function getListSuffix();

    /**
     * Sets form suffix
     *
     * @param string $suffix
     * @return self
     */
    public function setFormSuffix($suffix);

    /**
     * Retrieves form suffix
     *
     * @return string
     */
    public function getFormSuffix();

    /**
     * Sets template prefix
     *
     * @param string $prefix
     * @return self
     */
    public function setTemplatePrefix($prefix);

    /**
     * Retrieves template prefix
     *
     * @return string
     */
    public function getTemplatePrefix();

    /**
     * Sets create template
     *
     * @param string $template
     * @return self
     */
    public function setCreateTemplate($template);

    /**
     * Retrieves create template
     *
     * @return string
     */
    public function getCreateTemplate();

    /**
     * Sets read template
     *
     * @param string $template
     * @return self
     */
    public function setReadTemplate($template);

    /**
     * Retrieves read template
     *
     * @return string
     */
    public function getReadTemplate();

    /**
     * Sets update template
     *
     * @param string $template
     * @return self
     */
    public function setUpdateTemplate($template);

    /**
     * Retrieves update template
     *
     * @return string
     */
    public function getUpdateTemplate();

    /**
     * Sets delete template
     *
     * @param string $template
     * @return self
     */
    public function setDeleteTemplate($template);

    /**
     * Retrieves delete template
     *
     * @return string
     */
    public function getDeleteTemplate();

    /**
     * Sets list template
     *
     * @param string $template
     * @return self
     */
    public function setListTemplate($template);

    /**
     * Retrieves list template
     *
     * @return string
     */
    public function getListTemplate();

    /**
     * Sets form template
     *
     * @param string $template
     * @return self
     */
    public function setFormTemplate($template);

    /**
     * Retrieves form template
     *
     * @return string
     */
    public function getFormTemplate();
}
