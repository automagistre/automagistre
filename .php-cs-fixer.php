<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/bin',
        __DIR__.'/config',
        __DIR__.'/easyadmin',
        __DIR__.'/fork',
        __DIR__.'/public',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->exclude('public/bundles')
    ->exclude('public/css')
    ->exclude('public/fonts')
    ->exclude('public/js');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PSR12' => true,
        '@PSR12:risky' => true,
        'doctrine_annotation_braces' => ['syntax' => 'without_braces'],
        'blank_line_before_statement' => [
            'statements' => [
                'continue',
                'do',
                'exit',
                'goto',
                'if',
                'return',
                'switch',
                'throw',
                'try',
            ],
        ],
        'declare_strict_types' => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'php_unit_internal_class' => false,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_order_by_value' => ['annotations' => ['throws']],
        'yoda_style' => true,
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements' => ['arrays', 'arguments', 'parameters']
        ],
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/var/.php-cs-fixer.cache');
