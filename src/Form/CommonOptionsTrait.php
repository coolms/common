<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form;

use Zend\Captcha\AdapterInterface;

trait CommonOptionsTrait
{
    /**
     * @var string
     */
    protected $formLabel;

    /**
     * @var int
     */
    protected $formTimeout = 300;

    /**
     * @var array|\Traversable|AdapterInterface
     */
    protected $captchaOptions = [];

    /**
     * @var bool
     */
    protected $useCaptcha;

    /**
     * @var bool
     */
    protected $useSubmitElement = true;

    /**
     * @var bool
     */
    protected $useResetElement;

    /**
     * {@inheritDoc}
     */
    public function setFormLabel($label)
    {
        $this->formLabel = $label;
    
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getFormLabel()
    {
        return $this->formLabel;
    }

    /**
     * {@inheritDoc}
     */
    public function setFormTimeout($ttl)
    {
        $this->formTimeout = (int) $ttl;
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormTimeout()
    {
        return $this->formTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function setCaptchaOptions($options)
    {
        $this->captchaOptions = $options;
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCaptchaOptions()
    {
        return $this->captchaOptions;
    }

    /**
     * {@inheritDoc}
     */
    public function setUseCaptcha($useCaptcha)
    {
        $this->useCaptcha = (bool) $useCaptcha;
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUseCaptcha()
    {
        return $this->useCaptcha;
    }

    /**
     * {@inheritDoc}
     */
    public function setUseSubmitElement($flag)
    {
        $this->useSubmitElement = (bool) $flag;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUseSubmitElement()
    {
        return $this->useSubmitElement;
    }

    /**
     * {@inheritDoc}
     */
    public function setUseResetElement($flag)
    {
        $this->useResetElement = (bool) $flag;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUseResetElement()
    {
        return $this->useResetElement;
    }
}
