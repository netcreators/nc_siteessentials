<?php

$GLOBALS['TCA']['pages']['ctrl']['requestUpdate'] = 'is_siteroot';



# TCA configurations for the xml sitemap functionality
$tempColumns = [];
$tempColumns['tx_ncsiteessentials_xmlsitemap_exclude'] = [
    'exclude' => 1,
    'label' => 'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_exclude',
    'displayCond' => 'FIELD:doktype:=:1',
    'config' => [
        'type' => 'check',
        'items' => [
            1 => [
                0 => 'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_exclude.0',
            ],
        ],
    ],
];

$tempColumns['tx_ncsiteessentials_xmlsitemap_changefreq'] = [
    'exclude' => 0,
    'label' => 'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_changefreq',
    'displayCond' => 'FIELD:doktype:=:1',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            [
                'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_changefreq.0',
                ''
            ],
            [
                'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_changefreq.1',
                'always'
            ],
            [
                'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_changefreq.2',
                'hourly'
            ],
            [
                'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_changefreq.3',
                'daily'
            ],
            [
                'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_changefreq.4',
                'weekly'
            ],
            [
                'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_changefreq.5',
                'monthly'
            ],
            [
                'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_changefreq.6',
                'yearly'
            ],
            [
                'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tx_ncsiteessentials_xmlsitemap_changefreq.7',
                'never'
            ],
        ],
        'size' => 1,
        'maxitems' => 1,
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'miscellaneous',
    'tx_ncsiteessentials_xmlsitemap_exclude'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    'tx_ncsiteessentials_xmlsitemap_changefreq',
    '',
    'after:tx_ncsiteessentials_xmlsitemap_exclude'
);



# TCA configurations for the robots txt functionality
$tempColumns = [];
$tempColumns['tx_ncsiteessentials_robotstxt_content'] = [
    'exclude' => 0,
    'label' => 'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tabs.ncrobotstxt.tx_ncsiteessentials_robotstxt_content',
    'displayCond' => 'FIELD:is_siteroot:=:1',
    'config' => [
        'type' => 'text',
        'cols' => 40,
        'rows' => 10,
        'eval' => ''
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--div--;LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tabs.ncrobotstxt,tx_ncsiteessentials_robotstxt_content'
);



# TCA configurations for the google analytics functionality
$tempColumns = [];
$tempColumns['tx_ncsiteessentials_googleanalytics_content'] = [
    'exclude' => 0,
    'label' => 'LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tabs.ncgoogleanalytics.tx_ncsiteessentials_googleanalytics_content',
    'displayCond' => 'FIELD:is_siteroot:=:1',
    'config' => [
        'type' => 'text',
        'cols' => 40,
        'rows' => 10,
        'eval' => ''
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    '--div--;LLL:EXT:nc_siteessentials/Resources/Private/Language/locallang_db.xlf:tabs.ncgoogleanalytics,tx_ncsiteessentials_googleanalytics_content'
);