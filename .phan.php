<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 */
return [
    // A list of files to include in analysis
    'file_list'                       => [
        'vendor/csa/guzzle-bundle/src/CsaGuzzleBundle.php',
        'vendor/egeloen/ckeditor-bundle/IvoryCKEditorBundle.php',
        'vendor/ramsey/uuid/src/Uuid.php',
        'vendor/ramsey/uuid/src/UuidInterface.php',
        'vendor/friendsofsymfony/user-bundle/Model/User.php',
        'vendor/friendsofsymfony/user-bundle/Event/FormEvent.php',
        'vendor/friendsofsymfony/user-bundle/FOSUserEvents.php',
        'vendor/friendsofsymfony/user-bundle/FOSUserBundle.php',
        'vendor/giggsey/libphonenumber-for-php/src/PhoneNumber.php',
        'vendor/giggsey/libphonenumber-for-php/src/PhoneNumberFormat.php',
        'vendor/grachevko/enum/src/Enum.php',
        'vendor/misd/phone-number-bundle/MisdPhoneNumberBundle.php',
        'vendor/moneyphp/money/src/Money.php',
        'vendor/moneyphp/money/src/Currency.php',
        'vendor/moneyphp/money/src/MoneyFormatter.php',
        'vendor/pagerfanta/pagerfanta/src/Pagerfanta/Pagerfanta.php',
        'vendor/sentry/sentry-symfony/src/Sentry/SentryBundle/SentryBundle.php',
    ],

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list'                  => [
        'src',
        'vendor/doctrine',
        'vendor/friendsofsymfony/user-bundle/Model',
        'vendor/guzzlehttp',
        'vendor/javiereguiluz',
        'vendor/psr',
        'vendor/sensio',
        'vendor/symfony',
        'vendor/twig',
    ],

    // A directory list that defines files that will be excluded
    // from static analysis, but whose class and method
    // information should be included.
    //
    // Generally, you'll want to include the directories for
    // third-party code (such as "vendor/") in this list.
    //
    // n.b.: If you'd like to parse but not analyze 3rd
    //       party code, directories containing that code
    //       should be added to both the `directory_list`
    //       and `exclude_analysis_directory_list` arrays.
    'exclude_analysis_directory_list' => [
        'vendor/',
    ],

    // Add any issue types (such as 'PhanUndeclaredMethod')
    // here to inhibit them from being reported
    'suppress_issue_types'            => [
        'PhanParamSignatureMismatch',
        'PhanUndeclaredVariable',
    ],
];
