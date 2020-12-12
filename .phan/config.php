<?php
/**
 * PHAN configuration
 *
 * @package skaut-google-drive-documents
 */

return array(
	'target_php_version'                        => '8.0',
	'minimum_target_php_version'                => '5.6',
	'backward_compatibility_checks'             => false, // Covered by PHPCS.
	'warn_about_undocumented_throw_statements'  => true,
	'strict_method_checking'                    => true,
	'strict_object_checking'                    => true,
	'strict_property_checking'                  => true,
	'strict_return_checking'                    => true,
	'constant_variable_detection'               => true,
	'redundant_condition_detection'             => true,
	'unused_variable_detection'                 => true,
	'warn_about_redundant_use_namespaced_class' => true,
	'directory_list'                            => array(
		'src',
		'.phan',
		'dist/includes/vendor',
		'vendor/skaut/phan-wordpress-stubs/stubs',
	),
	'exclude_analysis_directory_list'           => array(
		'dist/includes/vendor/',
		'vendor/skaut/phan-wordpress-stubs/stubs',
	),
	'plugins'                                   => array(
		'AlwaysReturnPlugin',
		'DollarDollarPlugin',
		'DuplicateArrayKeyPlugin',
		'DuplicateExpressionPlugin',
		'EmptyStatementListPlugin',
		'InvalidVariableIssetPlugin',
		'NoAssertPlugin',
		'NonBoolBranchPlugin',
		'NonBoolInLogicalArithPlugin',
		'PossiblyStaticMethodPlugin',
		'PreferNamespaceUsePlugin',
		'PregRegexCheckerPlugin',
		'StrictComparisonPlugin',
		'SuspiciousParamOrderPlugin',
		'UnreachableCodePlugin',
		'UnusedSuppressionPlugin',
		'UseReturnValuePlugin',
	),
);
