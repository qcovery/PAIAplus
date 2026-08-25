<?php
return [
    'css' => ['paiaplus.css'],
    'helpers' => [
        'factories' => [
            'PAIAplus\View\Helper\PAIAplus\PatronInfo' => 'PAIAplus\View\Helper\PAIAplus\PatronInfoFactory',
        ],
        'aliases' => [
            'PatronInfo' => 'PAIAplus\View\Helper\PAIAplus\PatronInfo',
        ]
    ],
];
