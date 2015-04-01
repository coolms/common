<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper\Traits;

use Zend\Form\ElementInterface,
    Zend\I18n\Translator\TranslatorAwareInterface;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait TranslatorTextDomainTrait
{
    /**
     * @var string
     */
    protected $rollbackTranslatorTextDomain;

    /**
     * @param ElementInterface $element
     * @return string
     */
    public function getTranslatorTextDomain(ElementInterface $element = null)
    {
        if (!($element && ($textDomain = $element->getOption('text_domain')))
            && $this instanceof TranslatorAwareInterface
        ) {
            return parent::getTranslatorTextDomain();
        }

        if (empty($textDomain)) {
            return 'default';
        }

        if (null === $this->rollbackTranslatorTextDomain) {
            $this->rollbackTranslatorTextDomain = parent::getTranslatorTextDomain();
        }

        return $textDomain;
    }

    /**
     * @return self
     */
    protected function rollbackTranslatorTextDomain()
    {
        if (null !== $this->rollbackTranslatorTextDomain) {
            $this->setTranslatorTextDomain($this->rollbackTranslatorTextDomain);
        }

        return $this;
    }
}
