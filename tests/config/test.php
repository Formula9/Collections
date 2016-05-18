<?php

return [
    'name'      => [
        'first'   => 'James',
        'initial' => 'H',
        'last'    => 'Horner',
    ],
    'address'   => [
        'address'  => '123 14th Ave E.',
        'city'     => 'Vancouver',
        'province' => 'BC',
        'country'  => 'Canada',
        'postal'   => 'A1B 2C3',
    ],
    'telephone' => [
        'land'   => 'n/a',
        'mobile' => '789-011-2345',
    ],
    'title'     => 'Blade Test Template',
    'blade'     => [
        'cache'          => __DIR__ . '/cache',
        'template_paths' => [
            __DIR__ . '/templates',
        ],
    ],
    'twig'      => [
        'type'        => 4, // twig loader will use files
        'filesystem'  => [
            __DIR__ . '/templates',
        ],
        'environment' => [
            'cache'       => __DIR__ . '/cache',
            'debug'       => TRUE,
            'auto_reload' => TRUE,
        ],
    ],
    'markdown'  => [
        'debug'               => TRUE,
        'class'               => 'MarkdownExtra', // options: Markdown, MarkdownExtra, GithubMarkdown
        'template_paths'      => [
            __DIR__ . '/templates',
        ],
        'html5'               => TRUE,
        'keepListStartNumber' => TRUE,
        'enableNewLines'      => FALSE,  // only significant if the class is GithubMarkdown.

    ],
];
