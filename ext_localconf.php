<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'TYPO3.' . $_EXTKEY,
	'Pi1',
	array(
		'XmlSitemap' => 'render',
		'RobotsTxt' => 'render',
		'GoogleAnalytics' => 'render',
		'Metatag' => 'render',
		
	),
	// non-cacheable actions
	array(
		'XmlSitemap' => 'render',
		'RobotsTxt' => 'render',
	)
);

if (!is_array($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'])) {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'] = array();
}

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['nc_siteessentials'] =
	'TYPO3\NcSiteessentials\Controller\TypoScriptFrontendController->contentPostProcOutput';