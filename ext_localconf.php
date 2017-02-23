<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Netcreators.' . $_EXTKEY,
    'Pi1',
    array(
        'XmlSitemap' => 'render',
        'RobotsTxt' => 'render',
        'GoogleAnalytics' => 'render',
        'Metatag' => 'render',

    ),
    // non-cacheable actions
    array(
        'RobotsTxt' => 'render',
    )
);

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'] = array();
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['nc_siteessentials'] =
    'Netcreators\NcSiteessentials\Hook\CMS\Frontend\Controller\TypoScriptFrontendControllerHook->contentPostProcOutput';