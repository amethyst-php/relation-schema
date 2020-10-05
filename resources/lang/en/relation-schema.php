<?php

return [
    'name' => 'Relation Schema',
    'description' => 'Create your own custom relations between Models',,
    'attributes' => [
        'data' => [
            'label' => 'Data',
            'description' => 'Which data should the relation attached to'
        ],
        'name' => [
            'label' => 'Name',
            'description' => 'What is the name of the relation?'
        ],
        'description' => [
            'label' => 'Description',
            'description' => 'A brief description'
        ],
        'type' => [
            'label' => 'Type',
            'description' => 'Which type is this relation?'
        ],
        'payload' => [
            'label' => 'Payload',
            'description' => 'A set of instruction to better define the relation, this changes based on the type of the relation'
        ]
    ]
];