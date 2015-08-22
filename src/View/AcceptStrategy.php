<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View;

use Zend\EventManager\EventManagerInterface,
    Zend\EventManager\ListenerAggregateInterface,
    Zend\EventManager\ListenerAggregateTrait,
    Zend\Feed\Writer\Feed,
    Zend\View\Model\FeedModel,
    Zend\View\Model\JsonModel,
    Zend\View\Renderer\FeedRenderer,
    Zend\View\Renderer\JsonRenderer,
    Zend\View\Renderer\PhpRenderer,
    Zend\View\Renderer\RendererInterface,
    Zend\View\ViewEvent;

class AcceptStrategy implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var FeedRenderer
     */
    protected $feedRenderer;

    /**
     * @var JsonRenderer
     */
    protected $jsonRenderer;

    /**
     * @var PhpRenderer
     */
    protected $phpRenderer;

    /**
     * __construct
     *
     * @param PhpRenderer $phpRenderer
     * @param JsonRenderer $jsonRenderer
     * @param FeedRenderer $feedRenderer
     */
    public function __construct(
        PhpRenderer $phpRenderer,
        JsonRenderer $jsonRenderer,
        FeedRenderer $feedRenderer
    ) {
        $this->phpRenderer = $phpRenderer;
        $this->jsonRenderer = $jsonRenderer;
        $this->feedRenderer = $feedRenderer;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = null)
    {
        if (null === $priority) {
            $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, [$this, 'selectRenderer']);
            $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, [$this, 'injectResponse']);
        } else {
            $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, [$this, 'selectRenderer'], $priority);
            $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, [$this, 'injectResponse'], $priority);
        }
    }

    /**
     * @param  ViewEvent $e The ViewEvent instance
     * @return RendererInterface
     */
    public function selectRenderer($e)
    {
        $request = $e->getRequest();
        $headers = $request->getHeaders();
        $model   = $e->getModel();

        // No Accept header? return PhpRenderer
        if (!$headers->has('accept')) {
            return $this->phpRenderer;
        }

        $accept = $headers->get('accept');
        /* @var $mediaType \Zend\Http\Header\Accept\FieldValuePart\AcceptFieldValuePart */
        foreach ($accept->getPrioritized() as $mediaType) {
            $mediaSubtype = $mediaType->getSubtype();
            if ($mediaSubtype === 'json') {
                if (!$model instanceof JsonModel &&
                    ($children = $model->getChildrenByCaptureTo('content', false))
                ) {
                    $this->jsonRenderer->setMergeUnnamedChildren(true);
                    foreach ($children as $child) {
                        if (!$child instanceof JsonModel) {
                            $child->setCaptureTo(null);
                        }
                    }
                }

                return $this->jsonRenderer;
            }

            if ($mediaSubtype === 'rss+xml' || $mediaSubtype === 'atom+xml') {
                $this->feedRenderer->setFeedType(substr($mediaSubtype, 0, strpos($mediaSubtype, '+')));
                if (!$model instanceof FeedModel &&
                    ($children = $model->getChildrenByCaptureTo('content', false))
                ) {
                    foreach ($children as $child) {
                        if (!$child instanceof FeedModel) {
                            $child->setCaptureTo(null);
                        }
                    }
                }

                return $this->feedRenderer;
            }
        }

        // Nothing matched; return PhpRenderer. Technically, we should probably
        // return an HTTP 415 Unsupported response.
        return $this->phpRenderer;
    }

    /**
     * @param  ViewEvent $e The ViewEvent instance
     * @return void
     */
    public function injectResponse($e)
    {
        $renderer = $e->getRenderer();
        $response = $e->getResponse();
        $result   = $e->getResult();

        if ($renderer === $this->jsonRenderer) {
            // JSON Renderer; set content-type header
            $headers = $response->getHeaders();
            $headers->addHeaderLine('content-type', 'application/json');
        } elseif ($renderer === $this->feedRenderer) {
            // Feed Renderer; set content-type header, and export the feed if
            // necessary
            $feedType  = $this->feedRenderer->getFeedType();
            $headers   = $response->getHeaders();
            $mediatype = 'application/'
                . (('rss' == $feedType) ? 'rss' : 'atom')
                . '+xml';
            $headers->addHeaderLine('content-type', $mediatype);

            // If the $result is a feed, export it
            if ($result instanceof Feed) {
                $result = $result->export($feedType);
            }
        } elseif ($renderer !== $this->phpRenderer) {
            // Not a renderer we support, therefor not our strategy. Return
            return;
        }

        // Inject the content
        $response->setContent($result);
    }
}
