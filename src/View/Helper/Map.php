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

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class Map extends HtmlContainer
{
    /**
     * @var string
     */
    protected $tagName = 'map';

    /**
     * @var Area
     */
    protected $areaHelper;

    /**
     * @var string
     */
    protected $defaultAreaHelper = 'area';

    /**
     * {@inheritDoc}
     */
    public function render($content, array $attribs = [])
    {
        $areas = [];
        if (is_array($content)) {
            $helper = $this->getAreaHelper();
            foreach ($content as $area) {
                $areas[] = $helper(null, $area);
            }
        }

        return parent::render(PHP_EOL . implode(PHP_EOL, $areas) . PHP_EOL, $attribs);
    }

    /**
     * @param Area $helper
     * @return self
     */
    public function setAreaHelper(Area $helper)
    {
        $this->areaHelper = $helper;
        return $this;
    }

    /**
     * @return Area
     */
    protected function getAreaHelper()
    {
        if (null === $this->areaHelper) {
            if (method_exists($this->view, 'plugin')) {
                $this->areaHelper = $this->view->plugin($this->defaultAreaHelper);
            }

            if (!$this->areaHelper instanceof Area) {
                $this->areaHelper = new Area();
                $this->areaHelper->setView($this->getView());
            }
        }

        return $this->areaHelper;
    }
}
