<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon;

return [
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ],
        ],
    ],
    'caches' => [
        'apc' => [
            'adapter' => [
                'name'      =>'apc',
                'options'   => [
                    'ttl'       => 7200,
                    'namespace' => __NAMESPACE__,
                ],
            ],
            'plugins' => [
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],
            ],
        ],
        'memcached' => [
            'adapter' => [
                'name' =>'memcached',
                'options' => [
                    'ttl' => 7200,
                    'namespace' => __NAMESPACE__,
                    'servers' => [
                        [
                            '127.0.0.1', 11211
                        ],
                    ],
                    'liboptions' => [
                        'COMPRESSION'       => true,
                        'binary_protocol'   => true,
                        'no_block'          => true,
                        'connect_timeout'   => 100,
                    ],
                ],
            ],
            'plugins' => [
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],
            ],
        ],
        'array' => [
            'adapter' => [
                'name' =>'memory',
                'options' => [
                    'ttl' => 7200,
                    'namespace' => __NAMESPACE__,
                ],
            ],
            'plugins' => [
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],
                'serializer',
            ],
        ],
    ],
    'controller_plugins' => [
        'aliases' => [
            'translate' => 'CmsCommon\Mvc\Controller\Plugin\Translate',
            'translatePlural' => 'CmsCommon\Mvc\Controller\Plugin\TranslatePlural',
        ],
        'factories' => [
            'CmsCommon\Mvc\Controller\Plugin\Translate'
                => 'CmsCommon\Factory\TranslateControllerPluginFactory',
            'CmsCommon\Mvc\Controller\Plugin\TranslatePlural'
                => 'CmsCommon\Factory\TranslatePluralControllerPluginFactory',
        ],
    ],
    'controllers' => [
        'abstract_factories' => [
            'CmsCommon\Mvc\Service\CrudControllerAbstractServiceFactory'
                => 'CmsCommon\Mvc\Service\CrudControllerAbstractServiceFactory',
            'CmsCommon\Mvc\Service\RestfulControllerAbstractServiceFactory'
                => 'CmsCommon\Mvc\Service\RestfulControllerAbstractServiceFactory',
        ],
    ],
    'domain_services' => [
        'abstract_factories' => [
            'CmsCommon\Service\DomainServiceAbstractServiceFactory'
                => 'CmsCommon\Service\DomainServiceAbstractServiceFactory',
        ],
        'initializers' => [
            'CmsCommon\Initializer\MvcTranslatorInitializer'
                => 'CmsCommon\Initializer\MvcTranslatorInitializer',
        ],
    ],
    'filters' => [
        'invokables' => [
            'BindableClosure' => 'CmsCommon\Filter\BindableClosure',
        ],
    ],
    'form_annotaion_builder' => [
        'cache' => 'array',
        'listeners' => [],
    ],
    'form_elements' => [
        'abstract_factories' => [
            'CmsCommon\Form\Annotation\FormAbstractServiceFactory'
                => 'CmsCommon\Form\Annotation\FormAbstractServiceFactory',
        ],
        'aliases' => [
            'Collection' => 'CmsCommon\Form\Element\Collection',
            'DateSelect' => 'CmsCommon\Form\Element\DateSelect',
            'DateSelectRange' => 'CmsCommon\Form\Element\DateSelectRange',
            'DateTimeSelect' => 'CmsCommon\Form\Element\DateTimeSelect',
            'Fieldset' => 'CmsCommon\Form\Fieldset',
            'Form' => 'CmsCommon\Form\Form',
            'MonthSelect' => 'CmsCommon\Form\Element\MonthSelect',
            'Number' => 'CmsCommon\Form\Element\Number',
            'StaticElement' => 'CmsCommon\Form\Element\StaticElement',
        ],
        'invokables' => [
            'Zend\Form\Element\Collection' => 'CmsCommon\Form\Element\Collection',
            'Zend\Form\Fieldset' => 'CmsCommon\Form\Fieldset',
            'Zend\Form\Form' => 'CmsCommon\Form\Form',
            'CmsCommon\Form\Element\DateSelect' => 'CmsCommon\Form\Element\DateSelect',
            'CmsCommon\Form\Element\DateSelectRange' => 'CmsCommon\Form\Element\DateSelectRange',
            'CmsCommon\Form\Element\DateTimeSelect' => 'CmsCommon\Form\Element\DateTimeSelect',
            'CmsCommon\Form\Element\MonthSelect' => 'CmsCommon\Form\Element\MonthSelect',
            'CmsCommon\Form\Element\Number' => 'CmsCommon\Form\Element\Number',
            'CmsCommon\Form\Element\StaticElement' => 'CmsCommon\Form\Element\StaticElement',
        ],
    ],
    'input_filters' => [
        'aliases' => [
            'InputFilter' => 'Zend\InputFilter\InputFilter',
        ],
        'invokables' => [
            'Zend\InputFilter\InputFilter' => 'CmsCommon\InputFilter\InputFilter',
        ],
    ],
    'listeners' => [
        'CmsCommon\Event\ModuleOptionsListener' => 'CmsCommon\Event\ModuleOptionsListener',
        'CmsCommon\Event\PhpSettingsListener' => 'CmsCommon\Event\PhpSettingsListener',
        'CmsCommon\Event\RouterCacheListener' => 'CmsCommon\Event\RouterCacheListener',
    ],
    'module_options_suffixes' => [
        'Options\ModuleOptionsInterface',
        'Options',
        'Options\ModuleOptions',
    ],
    'navigation_helpers' => [
        'aliases' => [
            'menu' => 'CmsCommon\View\Helper\Navigation\Menu',
        ],
        'invokables' => [
            'CmsCommon\View\Helper\Navigation\Menu' => 'CmsCommon\View\Helper\Navigation\Menu',
        ],
    ],
    'router' => [
        'cache' => 'array',
        'routes' => [
            'api' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/api',
                ],
                'may_terminate' => false,
            ],
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory'
                => 'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        ],
        'aliases' => [
            'ViewAcceptStrategy' => 'CmsCommon\View\AcceptStrategy',
            'Zend\Mvc\Service\FormAnnotationBuilderFactory' => 'CmsCommon\Form\Annotation\AnnotationBuilder',
        ],
        'factories' => [
            'Zend\Cache\Storage\StorageInterface' => 'Zend\Cache\Service\StorageCacheFactory',
            'Zend\Captcha\AdapterInterface' => 'CmsCommon\Factory\CaptchaFactory',
            'CmsCommon\Form\Annotation\AnnotationBuilder' => 'CmsCommon\Factory\Form\AnnotationBuilderFactory',
            'CmsCommon\Form\Options\FormAnnotationBuilder' => 'CmsCommon\Factory\Form\AnnotationBuilderOptionsFactory',
            'CmsCommon\Mvc\Router\Options\RouterCacheOptions' => 'CmsCommon\Factory\Mvc\RouterCacheOptionsFactory',
            'CmsCommon\View\AcceptStrategy' => 'CmsCommon\Factory\View\AcceptStrategyFactory',
            'DomainServiceManager' => 'CmsCommon\Factory\DomainServicePluginManagerFactory',
            'MapperManager' => 'CmsCommon\Factory\MapperPluginManagerFactory',
            'SessionContainerManager' => 'CmsCommon\Factory\SessionContainerPluginManagerFactory',
        ],
        'initializers' => [
            'CmsCommon\Initializer\MvcTranslatorInitializer' => 'CmsCommon\Initializer\MvcTranslatorInitializer',
        ],
        'invokables' => [
            'CmsCommon\Crypt\PasswordGeneratorInterface' => 'CmsCommon\Crypt\PasswordGenerator',
            'CmsCommon\Event\ModuleOptionsListener' => 'CmsCommon\Event\ModuleOptionsListener',
            'CmsCommon\Event\PhpSettingsListener' => 'CmsCommon\Event\PhpSettingsListener',
            'CmsCommon\Event\RouterCacheListener' => 'CmsCommon\Event\RouterCacheListener',
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
                'text_domain' => 'default',
            ],
            [
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php',
            ],
        ],
    ],
    'validators' => [
        'invokables' => [
            'Callback' => 'CmsCommon\Validator\Callback',
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'cookie' => 'CmsCommon\View\Helper\Cookie',
            'flashMessenger' => 'CmsCommon\View\Helper\FlashMessenger',
            'formElementErrors' => 'Zend\Form\View\Helper\FormElementErrors',
            'formMessages' => 'CmsCommon\Form\View\Helper\FormMessages',
            'params' => 'CmsCommon\View\Helper\Params',
        ],
        'factories' => [
            'Zend\Form\View\Helper\FormElementErrors'
                => 'CmsCommon\Factory\Form\View\Helper\FormElementErrorsFactory',
            'CmsCommon\Form\View\Helper\FormMessages'
                => 'CmsCommon\Factory\Form\View\Helper\FormMessagesFactory',
            'CmsCommon\View\Helper\Cookie' => 'CmsCommon\Factory\View\Helper\CookieFactory',
            'CmsCommon\View\Helper\FlashMessenger'
                => 'CmsCommon\Factory\View\Helper\FlashMessengerFactory',
            'CmsCommon\View\Helper\Params' => 'CmsCommon\Factory\View\Helper\ParamsFactory',
        ],
        'invokables' => [
            'area' => 'CmsCommon\View\Helper\Area',
            'assetPath' => 'CmsCommon\View\Helper\AssetPath',
            'cmsMessages' => 'CmsCommon\View\Helper\Messages',
            'dataList' => 'CmsCommon\View\Helper\DataList',
            'dateTime' => 'CmsCommon\View\Helper\DateTime',
            'decorator' => 'CmsCommon\View\Helper\Decorator\Decorator',
            'element' => 'CmsCommon\Form\View\Helper\Decorator\Element',
            'elementDescription' => 'CmsCommon\Form\View\Helper\Decorator\ElementDescription',
            'elementErrors' => 'CmsCommon\Form\View\Helper\Decorator\ElementErrors',
            'elementLabel' => 'CmsCommon\Form\View\Helper\Decorator\ElementLabel',
            'fieldset' => 'CmsCommon\Form\View\Helper\Decorator\Fieldset',
            'form' => 'CmsCommon\Form\View\Helper\Form',
            'formCaptcha' => 'CmsCommon\Form\View\Helper\FormCaptcha',
            'formCollection' => 'CmsCommon\Form\View\Helper\FormCollection',
            'formCsrf' => 'CmsCommon\Form\View\Helper\FormCsrf',
            'formDateSelect' => 'CmsCommon\Form\View\Helper\FormDateSelect',
            'formElement' => 'CmsCommon\Form\View\Helper\FormElement',
            'formHidden' => 'CmsCommon\Form\View\Helper\FormHidden',
            'formLabel' => 'CmsCommon\Form\View\Helper\FormLabel',
            'formMonthSelect' => 'CmsCommon\Form\View\Helper\FormMonthSelect',
            'formRow' => 'CmsCommon\Form\View\Helper\FormRow',
            'formStatic' => 'CmsCommon\Form\View\Helper\FormStatic',
            'htmlContainer' => 'CmsCommon\View\Helper\HtmlContainer',
            'jsonExpr' => 'CmsCommon\View\Helper\JsonExpr',
            'idNormalizer' => 'CmsCommon\View\Helper\IdNormalizer',
            'label' => 'CmsCommon\Form\View\Helper\Decorator\Label',
            'legend' => 'CmsCommon\Form\View\Helper\Decorator\Legend',
            'locale' => 'CmsCommon\View\Helper\DefaultLocale',
            'map' => 'CmsCommon\View\Helper\Map',
            'nl2p' => 'CmsCommon\View\Helper\Nl2p',
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'cms-common/crud/create' => __DIR__ . '/../view/cms-common/crud/create.phtml',
            'cms-common/crud/read' => __DIR__ . '/../view/cms-common/crud/read.phtml',
            'cms-common/crud/update' => __DIR__ . '/../view/cms-common/crud/update.phtml',
            'cms-common/crud/delete' => __DIR__ . '/../view/cms-common/crud/delete.phtml',
            'cms-common/crud/list' => __DIR__ . '/../view/cms-common/crud/list.phtml',
            'cms-common/crud/form' => __DIR__ . '/../view/cms-common/crud/form.phtml',
        ],
        'strategies' => [
            'ViewAcceptStrategy' => 'ViewAcceptStrategy',
        ],
    ],
];
