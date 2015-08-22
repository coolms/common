<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Permissions\Acl;

use Zend\Permissions\Acl;

interface AclAwareInterface
{
    /**
     * Sets ACL to use
     *
     * @param  Acl\AclInterface $acl ACL object.
     * @return self
     */
    public function setAcl(Acl\AclInterface $acl = null);

    /**
     * Returns ACL or null if it isn't set using {@link setAcl()} or
     * {@link setDefaultAcl()}
     *
     * @return Acl\AclInterface|null  ACL object or null
     */
    public function getAcl();

    /**
     * Checks if the helper has an ACL instance
     *
     * @return bool
     */
    public function hasAcl();

    /**
     * Sets whether ACL should be used
     *
     * @param  bool $useAcl
     * @return self
     */
    public function setUseAcl($useAcl = true);

    /**
     * Returns whether ACL should be used
     *
     * @return bool
     */
    public function getUseAcl();

    /**
     * Sets ACL role(s) to use
     *
     * @param  mixed $role [optional] role to set. Expects a string, an
     *                     instance of type {@link Acl\Role\RoleInterface}, or null. Default
     *                     is null, which will set no role.
     * @return self
     */
    public function setRole($role = null);
    
    /**
     * Returns ACL role to use, or null if it isn't set
     * using {@link setRole()} or {@link setDefaultRole()}
     *
     * @return string|Acl\Role\RoleInterface|null
     */
    public function getRole();
    
    /**
     * Checks if the helper has an ACL role
     *
     * @return bool
     */
    public function hasRole();
    
    /**
     * Sets default ACL to use if another ACL is not explicitly set
     *
     * @param  Acl\AclInterface $acl [optional] ACL object. Default is null, which
     *                      sets no ACL object.
     * @return void
     */
    public static function setDefaultAcl(Acl\AclInterface $acl = null);

    /**
     * Sets default ACL role(s) to use if not explicitly set later with {@link setRole()}
     *
     * @param  mixed $role [optional] role to set. Expects null, string, or an
     *                     instance of {@link Acl\Role\RoleInterface}. Default is null, which
     *                     sets no default role.
     * @return void
     */
    public static function setDefaultRole($role = null);
}
