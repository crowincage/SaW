<?php
namespace App\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class SwaggerDecorator
 *
 * @package App\Swagger
 * @author Christian Ruppel < post@christianruppel.de >
 */
class SwaggerDecorator implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $decorated;

    /**
     * SwaggerDecorator constructor.
     *
     * @param NormalizerInterface $decorated
     */
    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     *
     * @return array|\ArrayObject|bool|float|int|string|null
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $defaultIdParam = [
            'name' => 'id',
            'in' => 'path',
            'required' => true,
            'schema' => ['type' => 'integer']
        ];

        $docs['paths']['/api/saw/start']['post'] = [
            'tags' => ['SaW'],
            'summary' => 'Start a new SaW game',
            'requestBody' => [
                'description' => 'description',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'playerName' => [
                                    'type' => 'string'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'responses' => [
                201 => [
                    'description' => 'The created Game entity',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                '$ref' => '#/components/schemas/Game-game:read'
                            ]
                        ]
                    ],
                ],
                400 => [
                    'description' => 'Failed request params validation',
                    'content' => ['application/json' => []],
                ]
            ]
        ];

        $docs['paths']['/api/saw/{id}']['get'] = [
            'tags' => ['SaW'],
            'summary' => 'Get a SaW game',
            'responses' => [
                201 => [
                    'description' => 'The Game entity',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                '$ref' => '#/components/schemas/Game-game:read'
                            ]
                        ]
                    ],
                ],
                400 => [
                    'description' => 'Failed request params validation',
                    'content' => ['application/json' => []],
                ]
            ],
            'parameters' => [$defaultIdParam]
        ];

        $docs['paths']['/api/saw/{id}/place_ship']['put'] = [
            'tags' => ['SaW'],
            'summary' => 'Place a ship',
            'requestBody' => [
                'description' => 'description',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'coordinate' => ['type' => 'string'],
                                'shipId' => ['type' => 'integer'],
                                'direction' => [
                                    'type' => 'string',
                                    'enum' => ['horizontal', 'vertical']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'responses' => [
                201 => [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                '$ref' => '#/components/schemas/Board-board:read'
                            ]
                        ]
                    ],
                ],
                400 => [
                    'description' => 'Failed request params validation',
                    'content' => ['application/json' => []],
                ]
            ],
            'parameters' => [$defaultIdParam]
        ];

        // not yet implemented
        // $docs['paths']['/api/saw/{id}/replace_ships']['post'] = [
        //     'summary' => '',
        //     'consumes' => ['application/json'],
        //     'produces' => ['application/json'],
        //     'responses' => [],
        //     'parameters' => []
        // ];

        $docs['paths']['/api/saw/{id}/shot']['post'] = [
            'tags' => ['SaW'],
            'summary' => 'Make a shot',
            'requestBody' => [
                'description' => 'description',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'coordinate' => ['type' => 'string']
                            ]
                        ]
                    ]
                ]
            ],
            'responses' => [
                201 => [
                    'description' => 'The created Game entity',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'COORDINATE' => ['type' => 'boolean']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'parameters' => [$defaultIdParam],
        ];

        // not yet implemented
        // $docs['paths']['/api/saw/{id}/shot']['get'] = [
        //     'summary' => 'Get a bot shot',
        //     'responses' => [],
        //     'parameters' => [$defaultIdParam]
        // ];

        // not yet implemened
        // $docs['paths']['/api/saw/{id}/salve']['post'] = [
        //     'summary' => '',
        //     'responses' => [],
        //     'parameters' => [$defaultIdParam]
        // ];

        // $docs['paths']['/api/saw/{id}/salve']['get'] = [
        //     'summary' => '',
        //     'responses' => [],
        //     'parameters' => [$defaultIdParam]
        // ];

        $docs['info']['description'] = 'SaW (Ships at War) - battleship API';

        return $docs;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * 
     * @return bool
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
}