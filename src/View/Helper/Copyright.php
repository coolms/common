<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper;

use Zend\I18n\Translator,
    Zend\View\Helper\AbstractHelper;

class Copyright extends AbstractHelper implements Translator\TranslatorAwareInterface
{
    use Translator\TranslatorAwareTrait;

    const APP_NAME      = 'CoolMS2';
    const APP_VENDOR    = 'Altgraphic, ALC';

    /**
     * @var string
     */
    private $pattern = '%s &copy; 2011 - %d; %s';

    /**
     * @return string
     */
    public function __invoke()
    {
        $vendor = self::APP_VENDOR;
        if (null !== ($translator = $this->getTranslator())) {
            $vendor = $translator->translate($vendor, $this->getTranslatorTextDomain());
        }

        return sprintf(
            $this->pattern,
            self::APP_NAME,
            (new \DateTime('now'))->format('Y'),
            $vendor
        );
    }
}
