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
            'translate'         => 'CmsCommon\Mvc\Controller\Plugin\Translate',
            'translatePlural'   => 'CmsCommon\Mvc\Controller\Plugin\TranslatePlural',
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
            'CmsCommon\Mvc\Controller\CrudController'
                => 'CmsCommon\Mvc\Service\CrudControllerAbstractServiceFactory',
            'CmsCommon\Mvc\Controller\RestfulController'
                => 'CmsCommon\Mvc\Service\RestfulControllerAbstractServiceFactory',
        ],
    ],
    'domain_services' => [
        'abstract_factories' => [
            'CmsCommon\Persistence\Service\ServiceInterface'
                => 'CmsCommon\Service\DomainServiceAbstractServiceFactory',
        ],
    ],
    'form_elements' => [
        'abstract_factories' => [
            'CmsCommon\AnnotationForm' => 'CmsCommon\Form\Annotation\FormAbstractServiceFactory',
        ],
        'aliases' => [
            'Collection'        => 'CmsCommon\Form\Element\Collection',
            'DateSelect'        => 'CmsCommon\Form\Element\DateSelect',
            'DateTimeSelect'    => 'CmsCommon\Form\Element\DateTimeSelect',
            'Form'              => 'CmsCommon\Form\Form',
            'MonthSelect'       => 'CmsCommon\Form\Element\MonthSelect',
            'Number'            => 'CmsCommon\Form\Element\Number',
            'StaticElement'     => 'CmsCommon\Form\Element\StaticElement',
        ],
        'invokables' => [
            'CmsCommon\Form\Element\DateSelect'       => 'CmsCommon\Form\Element\DateSelect',
            'CmsCommon\Form\Element\DateTimeSelect'   => 'CmsCommon\Form\Element\DateTimeSelect',
            'CmsCommon\Form\Element\MonthSelect'      => 'CmsCommon\Form\Element\MonthSelect',
            'CmsCommon\Form\Element\Number'           => 'CmsCommon\Form\Element\Number',
            'CmsCommon\Form\Element\StaticElement'    => 'CmsCommon\Form\Element\StaticElement',
            'Zend\Form\Element\Collection'          => 'CmsCommon\Form\Element\Collection',
            'Zend\Form\Form'                        => 'CmsCommon\Form\Form',
        ],
    ],
    'listeners' => [
        'CmsCommon\EventListener\ModuleOptionsListener'   => 'CmsCommon\EventListener\ModuleOptionsListener',
        'CmsCommon\EventListener\PhpSettingsListener'     => 'CmsCommon\EventListener\PhpSettingsListener',
    ],
    'module_options_suffixes' => [
        'Options\ModuleOptionsInterface',
        'Options',
        'Options\ModuleOptions',
    ],
    'router' => [
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
            'Zend\Cache\Storage\StorageInterface'   => 'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        ],
        'factories' => [
            'Zend\Cache\Storage\StorageInterface'   => 'Zend\Cache\Service\StorageCacheFactory',
            'Zend\Captcha\AdapterInterface'         => 'CmsCommon\Factory\CaptchaFactory',
            'DomainServiceManager'                  => 'CmsCommon\Factory\DomainServicePluginManagerFactory',
            'MapperManager'                         => 'CmsCommon\Factory\MapperPluginManagerFactory',
            'SessionContainerManager'               => 'CmsCommon\Factory\SessionContainerPluginManagerFactory',
        ],
        'invokables' => [
            'CmsCommon\Crypt\PasswordGeneratorInterface'      => 'CmsCommon\Crypt\PasswordGenerator',
            'CmsCommon\EventListener\ModuleOptionsListener'   => 'CmsCommon\EventListener\ModuleOptionsListener',
            'CmsCommon\EventListener\PhpSettingsListener'     => 'CmsCommon\EventListener\PhpSettingsListener',
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'          => 'gettext',
                'base_dir'      => __DIR__ . '/../language',
                'pattern'       => '%s.mo',
                'text_domain'   => 'default',
            ],
            [
                'type'          => 'phpArray',
                'base_dir'      => __DIR__ . '/../language',
                'pattern'       => '%s.php',
            ],
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'dateTime'           => 'CmsCommon\View\Helper\DateTime',
            'decorator'          => 'CmsCommon\View\Helper\Decorator\Decorator',
            'element'            => 'CmsCommon\Form\View\Helper\Decorator\Element',
            'elementDescription' => 'CmsCommon\Form\View\Helper\Decorator\ElementDescription',
            'elementErrors'      => 'CmsCommon\Form\View\Helper\Decorator\ElementErrors',
            'elementLabel'       => 'CmsCommon\Form\View\Helper\Decorator\ElementLabel',
            'fieldset'           => 'CmsCommon\Form\View\Helper\Decorator\Fieldset',
            'form'               => 'CmsCommon\Form\View\Helper\Form',
            'formCaptcha'        => 'CmsCommon\Form\View\Helper\FormCaptcha',
            'formCollection'     => 'CmsCommon\Form\View\Helper\FormCollection',
            'formDateSelect'     => 'CmsCommon\Form\View\Helper\FormDateSelect',
            'formElement'        => 'CmsCommon\Form\View\Helper\FormElement',
            'formMessages'       => 'CmsCommon\Form\View\Helper\FormMessages',
            'formMonthSelect'    => 'CmsCommon\Form\View\Helper\FormMonthSelect',
            'formRow'            => 'CmsCommon\Form\View\Helper\FormRow',
            'formStatic'         => 'CmsCommon\Form\View\Helper\FormStatic',
            'htmlContainer'      => 'CmsCommon\View\Helper\HtmlContainer',
            'idNormalizer'       => 'CmsCommon\View\Helper\IdNormalizer',
            'legend'             => 'CmsCommon\Form\View\Helper\Decorator\Legend',
            'nl2p'               => 'CmsCommon\View\Helper\Nl2p',
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'cms-common/crud/create'  => __DIR__ . '/../view/cms-common/crud/create.phtml',
            'cms-common/crud/read'    => __DIR__ . '/../view/cms-common/crud/read.phtml',
            'cms-common/crud/update'  => __DIR__ . '/../view/cms-common/crud/update.phtml',
            'cms-common/crud/delete'  => __DIR__ . '/../view/cms-common/crud/delete.phtml',
            'cms-common/crud/list'    => __DIR__ . '/../view/cms-common/crud/list.phtml',
            'cms-common/crud/form'    => __DIR__ . '/../view/cms-common/crud/form.phtml',
        ],
    ],
];
