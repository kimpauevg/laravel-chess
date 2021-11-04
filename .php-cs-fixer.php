<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['bootstrap', 'storage'])
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR12'                      => true,
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try', 'foreach', 'if']
    ],
    'no_unused_imports'    => true,
    'no_extra_blank_lines' => true,
    'single_space_after_construct' => [
        'constructs' => ['return']
    ],
    'binary_operator_spaces' => [
        'default'   => null,
        'operators' => [
            '=>' => 'align_single_space_minimal',
        ]
    ],
    'cast_spaces' => true,
])->setFinder($finder);
