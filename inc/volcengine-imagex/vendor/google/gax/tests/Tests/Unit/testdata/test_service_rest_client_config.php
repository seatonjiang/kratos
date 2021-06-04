<?php

return [
    'interfaces' => [
        'test.interface.v1.api' => [
            'MethodWithBody' => [
                'method' => 'post',
                'uriTemplate' => '/v1/foo',
                'body' => '*',
            ],
            'MethodWithUrlPlaceholder' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=message/**}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName'
                        ]
                    ],
                ],
            ],
            'MethodWithBodyAndUrlPlaceholder' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=message/**}',
                'body' => '*',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName'
                        ]
                    ],
                ],
            ],
            'MethodWithNestedMessageAsBody' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=message/**}',
                'body' => 'nested_message',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName'
                        ]
                    ],
                ],
            ],
            'MethodWithScalarBody' => [
                'method' => 'post',
                'uriTemplate' => '/v1/foo',
                'body' => 'name',
            ],
            'MethodWithNestedUrlPlaceholder' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{nested_message=nested/**}',
                'body' => '*',
                'placeholders' => [
                    'nested_message' => [
                        'getters' => [
                            'getNestedMessage',
                            'getName'
                        ]
                    ]
                ],
            ],
            'MethodWithColonInUrl' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=message/*}:action',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ]
                ],
            ],
            'MethodWithMultipleWildcardsAndColonInUrl' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name=message/*}/number/{number=*}:action',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ]
                    ],
                    'number' => [
                        'getters' => [
                            'getNumber',
                        ]
                    ]
                ],
            ],
            'MethodWithSimplePlaceholder' => [
                'method' => 'get',
                'uriTemplate' => '/v1/{name}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ]
                ]
            ],
            'MethodWithAdditionalBindings' => [
                'method' => 'post',
                'uriTemplate' => '/v1/{name=message/**}/additional/bindings',
                'body' => '*',
                'additionalBindings' => [
                    [
                        'method' => 'post',
                        'uriTemplate' => '/v2/{nested_message=nested/**}/additional/bindings',
                        'body' => '*'
                    ],
                    [
                        'method' => 'post',
                        'uriTemplate' => '/v1/{name=different/format/**}/additional/bindings',
                        'body' => '*'
                    ],
                ],
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName'
                        ]
                    ],
                    'nested_message' => [
                        'getters' => [
                            'getNestedMessage',
                            'getName'
                        ]
                    ]
                ],
            ],
            'MethodWithSpecialJsonMapping' => [
                'method' => 'get',
                'uriTemplate' => '/v1/special/mapping'
            ],
            'MethodWithoutPlaceholders' => [
                'method' => 'get',
                'uriTemplate' => '/v1/fixedurl',
            ],
        ],
    ],
];
