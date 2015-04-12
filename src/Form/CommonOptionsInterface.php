<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form;

use Zend\Captcha\AdapterInterface;

interface CommonOptionsInterface
{
    /**
     * Sets form label
     *
     * @param string $label
     * @return self
     */
    public function setFormLabel($label);

    /**
     * Retrieves form label
     *
     * @return string
     */
    public function getFormLabel();

    /**
     * Sets form TTL in seconds
     *
     * @param int $ttl
     * @return self
     */
    public function setFormTimeout($ttl);

    /**
     * Retrieves form TTL in seconds
     *
     * @return int
     */
    public function getFormTimeout();

    /**
     * Sets CAPTCHA options
     *
     * @param array|\Traversable|AdapterInterface $options
     * @return self
     */
    public function setCaptchaOptions($options);

    /**
     * Retrieves CAPTCHA options
     *
     * @return array|\Traversable|AdapterInterface
     */
    public function getCaptchaOptions();

    /**
     * Sets whether use CAPTCHA
     *
     * @param bool $flag
     * @return self
     */
    public function setUseCaptcha($flag);

    /**
     * Gets whether use CAPCTHA
     *
     * @return bool
     */
    public function getUseCaptcha();

    /**
     * @param bool $flag
     * @return self
     */
    public function setUseSubmitElement($flag);

    /**
     * @return bool
     */
    public function getUseSubmitElement();

    /**
     * @param bool $flag
     * @return self
     */
    public function setUseResetElement($flag);

    /**
     * @return bool
     */
    public function getUseResetElement();
}
