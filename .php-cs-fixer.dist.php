<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->exclude('var')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['const', 'class', 'function'],
        ],
        'no_unused_imports' => true,
        'single_line_throw' => false,
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays', 'arguments', 'parameters'],
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],
        'nullable_type_declaration_for_default_null_value' => true,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'concat_space' => ['spacing' => 'one'],
        'cast_spaces' => ['space' => 'single'],
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'method' => 'one',
                'property' => 'one',
            ],
        ],
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
            'remove_inheritdoc' => true,
        ],
        'yoda_style' => false,
    ])
    ->setFinder($finder)
;
