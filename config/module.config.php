<?php
namespace PAIAplus\Module\Config;

$config = [
    'service_manager' => [
        'allow_override' => true,
        'factories' => [
            'PAIAplus\Auth\ILSAuthenticator' => 'PAIAplus\Auth\ILSAuthenticatorFactory',
        ],
        'aliases' => [
            'VuFind\Auth\ILSAuthenticator' => 'PAIAplus\Auth\ILSAuthenticator',
            'VuFind\ILSAuthenticator' => 'PAIAplus\Auth\ILSAuthenticator',
        ],
    ],
    'vufind' => [
        'plugin_managers' => [
            'ils_driver' => [
                'factories' => [
                    'PAIAplus\ILS\Driver\PAIA' => 'PAIAplus\ILS\Driver\PAIAFactory',
                ],
                'aliases' => [
                    'paia' => 'PAIAplus\ILS\Driver\PAIA',
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            'PAIAplus\Controller\RecordController' => 'PAIAplus\Controller\AbstractBaseWithConfigFactory',
            'PAIAplus\Controller\MyResearchController' => 'PAIAplus\Controller\MyResearchControllerFactory',
        ],
        'aliases' => [
            'Record' => 'PAIAplus\Controller\RecordController',
            'record' => 'PAIAplus\Controller\RecordController',
            'MyResearch' => 'PAIAplus\Controller\MyResearchController',
            'myresearch' => 'PAIAplus\Controller\MyResearchController',
        ],
    ],
];

return $config;
