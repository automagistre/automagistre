#!/usr/bin/env php
<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('public/bundles')
    ->exclude('public/css')
    ->exclude('public/fonts')
    ->exclude('public/js');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@DoctrineAnnotation'                   => true,
        '@PhpCsFixer'                           => true,
        '@PhpCsFixer:risky'                     => true,
        '@Symfony'                              => true,
        '@Symfony:risky'                        => true,
        'array_syntax'                          => ['syntax' => 'short'],
        'binary_operator_spaces'                => ['operators' => ['=>' => 'single_space']],
        'fopen_flags'                           => ['b_mode' => true],
        'fully_qualified_strict_types'          => true,
        'global_namespace_import'               => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'heredoc_indentation'                   => true,
        'linebreak_after_opening_tag'           => true,
        'list_syntax'                           => ['syntax' => 'short'],
        'multiline_whitespace_before_semicolons'=> false,
        'native_function_invocation'            => ['include' => [], 'strict' => true],
        'no_null_property_initialization'       => true,
        'no_unneeded_final_method'              => false,
        'no_unreachable_default_argument_value' => true,
        'no_unused_imports'                     => true,
        'no_useless_else'                       => true,
        'no_useless_return'                     => true,
        'non_printable_character'               => false,
        'not_operator_with_space'               => false,
        'not_operator_with_successor_space'     => false,
        'ordered_class_elements'                => true,
        'ordered_imports'                       => true,
        'php_unit_internal_class'               => false,
        'php_unit_strict'                       => true,
        'php_unit_test_class_requires_covers'   => false,
        'phpdoc_order'                          => true,
        'phpdoc_to_comment'                     => false,
        'phpdoc_types_order'                    => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
        'random_api_migration'                  => true,
        'simplified_null_return'                => false,
        'single_line_throw'                     => false,
        'strict_comparison'                     => true,
        'strict_param'                          => true,
        'ternary_to_null_coalescing'            => true,
        'void_return'                           => true,
        'php_unit_test_case_static_method_calls'=> ['call_type' => 'self'],
        'PedroTroller/doctrine_migrations'      => true,
        'PedroTroller/line_break_between_method_arguments' => ['max-args' => 10, 'max-length' => 120],
        'PedroTroller/useless_comment'          => true,
        'PedroTroller/line_break_between_statements' => true,
    ])
    ->setFinder($finder)
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
    ->setCacheFile(__DIR__.'/var/.php_cs.cache');
