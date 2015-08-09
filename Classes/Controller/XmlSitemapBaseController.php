<?php
namespace TYPO3\NcSiteessentials\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Arek van Schaijk <arek@netcreators.nl>, Netcreators
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\NcSiteessentials\Exception;

/**
 * XmlSitemapBaseController
 */
class XmlSitemapBaseController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var array $pagesArray
	 */
	public $pages = array();
	
	/**
	 * @var array $sitemap
	 */
	public $sitemap = array();
	
	/**
	 * @var boolean $hasChangeFreq
	 */
	public $hasChangeFreq = false;
	
	/**
	 * @var array $allowedDoktypes
	 */
	public $allowedDoktypes = array();

    /**
     * @var array
     */
    protected $fields = NULL;
	
	/**
	 * Render Allowed Doktype Array
	 *
	 * @return void
	 */
	protected function renderAllowedDoktypeArray() {
		$this->allowedDoktypes = GeneralUtility::trimExplode(',', $this->settings['xml_sitemap']['doktypes'], true);
	}
	
	/**
	 * Render Pages Relation Array
	 *
	 * @param object $pages
	 * @return void
	 */
	protected function renderPagesRelationArray($pages) {
		
		// handle page one by one and generates a custom array with details about the relations
		foreach($pages as $page) {
			
			$changeFreq = $page->getChangeFreq();
			$this->pages[$page->getUid()]['doktype']	= $page->getDoktype();
			$this->pages[$page->getUid()]['navHide']	= $page->getNavHide();
			$this->pages[$page->getUid()]['exclude']	= $page->isExcludeInSitemap();
			$this->pages[$page->getUid()]['timestamp']	= $page->getTstamp();
			$this->pages[$page->getUid()]['changeFreq']	= $changeFreq;
			
			if(!empty($changeFreq))				
				$this->hasChangeFreq = true;
			
			if($page->getPid()) {
				
				if(!$this->pages[$page->getPid()]['subs'])
					$this->pages[$page->getPid()]['subs'] = array();
					
				$this->pages[$page->getPid()]['subs'][]	= $page->getUid();
				$this->pages[$page->getUid()]['subOf']	= $page->getPid();
			}
		}
	}
	
	/**
	 * Insert Page And Subpages In Sitemap
	 *
	 * @param integer $pid
	 * @return void
	 */
	protected function insertPageAndSubpagesInSitemap($pid) {
		
		// checks the make excluded pages recursively setting
		if(!$this->settings['xml_sitemap']['makeExcludedPagesRecursively'] || ($this->settings['xml_sitemap']['makeExcludedPagesRecursively'] && !$this->pages[$pid]['exclude'])) {
			
			// insert page and subpages in sitemap
			$this->insertPageInSitemap($pid);
			
			// if page has subpages, loop all subs and insert them in the sitemap
			if($this->pages[$pid]['subs']) {
				
				foreach($this->pages[$pid]['subs'] as $sub) {
					
					$this->insertPageAndSubpagesInSitemap($sub);
				}
			}
		}
	}
	
	/**
	 * Insert Page In Sitemap
	 *
	 * @param integer $pid
	 * @return void
	 */
	protected function insertPageInSitemap($pid) {
		
		// include only pages of the doktype '1' (NORMAL)
		if(in_array($this->pages[$pid]['doktype'], $this->allowedDoktypes)) {
			
			// check settings for hide in menu pages
			if($this->settings['xml_sitemap']['includeHideInMenu'] || (!$this->settings['xml_sitemap']['includeHideInMenu'] && !$this->pages[$pid]['navHide'])) {
				
				// checks if the page is excluded for this sitemap
				if(!$this->pages[$pid]['exclude']) {

					// add page to sitemap
					$this->addPageToSitemap($pid, $this->pages[$pid]['timestamp'], $this->pages[$pid]['changeFreq']);
				}
			}
		}
	}
	
	/**
	 * Add Page To Sitemap
	 *
	 * @param integer $pageUid
	 * @param integer $timestamp
	 * @param string $changeFreq
	 * @param array $params
	 * @return void
	 */
	protected function addPageToSitemap($pageUid, $timestamp, $changeFreq, $params = array()) {

		$this->sitemap[] = array(
			'pageUid'		=> $pageUid,
			'params'		=> $params,
			'timestamp'		=> $timestamp,
			'changeFreq'	=> $changeFreq,
		);
	}
	
	/**
	 * Make Change Freq Recursively
	 *
	 * @param integer $rootLineUid
	 * @return void
	 */
	protected function makeChangeFreqRecursively($rootLineUid) {
		
		// check if there is a page with a change freq set
		if($this->hasChangeFreq) {
			
			// set change freq recursively on subpages
			$this->setChangeFreqRecursivelyOnSubpages($rootLineUid, $this->pages[$rootLineUid]['changeFreq']);
		}

	}
	
	/**
	 * Set Change Freq Recursively On Subpages
	 *
	 * @param integer $pid
	 * @param string $changeFreq
	 * @return void
	 */
	protected function setChangeFreqRecursivelyOnSubpages($pid, $changeFreq) {
			
		// if $pid has subpages
		if($this->pages[$pid]['subs']) {
			
			// foreach subpages of $pid
			foreach($this->pages[$pid]['subs'] as $page) {
				
				// if the subpage had no own changefreq set we should use the changefreq from the previous level
				if(empty($this->pages[$page]['changeFreq']) && !empty($changeFreq)) {
					
					$this->pages[$page]['changeFreq'] = $changeFreq;
					$this->setChangeFreqRecursivelyOnSubpages($page, $changeFreq);
					
				// otherwise, we set here a new changefreq
				} else {
					
					$this->setChangeFreqRecursivelyOnSubpages($page, $this->pages[$page]['changeFreq']);
					
				}
			}
		}
	}
	
	/**
	 * Insert Custom Mapped Pages In Sitemap
	 *
	 * @param integer $currentPid
	 * @return void
	 */
	protected function insertCustomMappedPagesInSitemap($currentPid) {
        if ($this->settings['xml_sitemap']['mapping'] && is_array($this->settings['xml_sitemap']['mapping'])) {
            foreach ($this->settings['xml_sitemap']['mapping'] as $configuration) {
                $showOnPids = FALSE;
                if ($configuration['sitemapPids']) {
                    if (strpos($configuration['sitemapPids'], ',') !== FALSE) {
                        $showOnPids = GeneralUtility::trimExplode(',', $configuration['sitemapPids']);
                    } else {
                        $showOnPids = array($configuration['sitemapPids']);
                    }
                }
                if (!$showOnPids || in_array($currentPid, $showOnPids)) {
                    $this->handleCustomMapping($configuration);
                }
            }
        }
	}
	
	/**
	 * Handle Custom Mapping
	 *
	 * @global \TYPO3\CMS\Core\Database\DatabaseConnection $TYPO3_DB
     * @global array $TCA
	 * @param array $configuration
	 * @return void
	 */
	protected function handleCustomMapping($configuration) {
		
		global $TYPO3_DB, $TCA;

        $this->fields = array();
        if ($configuration['record']['tableName'] && $TCA[$configuration['record']['tableName']]) {
            $fieldTimestamp = $TCA[$configuration['record']['tableName']]['ctrl']['tstamp'];
            if ($configuration['record']['timestamp']['field']) {
                $fieldTimestamp = $configuration['record']['timestamp']['field'];
            }
            $this->fields[] = $fieldTimestamp.' AS timestamp';
        }

		if ($configuration['record']['params'] && is_array($configuration['record']['params'])) {
        	$this->searchRecursivelyForFields($configuration['record']['params']);
		}
		
		$where = array();
		if ($configuration['record']['where']) {
			$where[] = $configuration['record']['where'];	
		}
		
		// deleted
		if ($TCA[$configuration['record']['tableName']]['ctrl']['delete']) {
			$where[] = $TCA[$configuration['record']['tableName']]['ctrl']['delete'].' = 0';
		}
		
		// hidden
		if ($TCA[$configuration['record']['tableName']]['ctrl']['enablecolumns']['disabled']) {
			$where[] = $TCA[$configuration['record']['tableName']]['ctrl']['enablecolumns']['disabled'].' = 0';
		}
		
		// starttime
		if ($TCA[$configuration['record']['tableName']]['ctrl']['enablecolumns']['starttime']) {
			$where[] = '('.$TCA[$configuration['record']['tableName']]['ctrl']['enablecolumns']['starttime'].' = 0 OR '.$TCA[$configuration['record']['tableName']]['ctrl']['enablecolumns']['starttime'].' >= '.time().')';
		}
		
		// endtime
		if ($TCA[$configuration['record']['tableName']]['ctrl']['enablecolumns']['endtime']) {
			$where[] = '('.$TCA[$configuration['record']['tableName']]['ctrl']['enablecolumns']['endtime'].' = 0 OR '.$TCA[$configuration['record']['tableName']]['ctrl']['enablecolumns']['endtime'].' <= '.time().')';
		}
		
		if ($configuration['record']['storagePids']) {
			$pids = array();
			if (strpos($configuration['record']['storagePids'], ',') !== FALSE) {
				foreach (GeneralUtility::trimExplode(',', $configuration['record']['storagePids']) as $pid) {
					if (ctype_digit($pid)) {
						$pids[] = $pid;	
					}
				}
			} else if (ctype_digit($configuration['record']['storagePids'])) {
				$pids[] = $configuration['record']['storagePids'];	
			}
			
			if ($pids) {
				if (count($pids) == 1) {
					$where[] = 'pid = '.$pids[0];
				} else {
					$where[] = 'pid IN('.implode(',', $pids).')';
				}
			}
		}
		
		$changeFreq = ($configuration['changeFreq'] ? :$this->settings['xml_sitemap']['defaultChangeFreq']); 
			
        $rows = $TYPO3_DB->exec_SELECTgetRows(implode(',', $this->fields), $configuration['record']['tableName'], implode(' AND ', $where), NULL, 'uid DESC');
		foreach ($rows as $row) {
			$this->params = array();
			$params = $this->setParametersRecursively($configuration['record']['params'], $row);
			$this->addPageToSitemap($configuration['record']['singlePid'], $row['timestamp'], $changeFreq, $params);
		}
	}
	
	/**
	 * Search Recursively For Fields
	 *
	 * @param array $array
	 * @return void
	 */
	protected function searchRecursivelyForFields($array) {
		foreach ($array as $key => $value) {
			if ($key === 'field') {
				$this->fields[] = $value;	
			} else if(is_array($value)) {
				$this->searchRecursivelyForFields($value);
			}
		}
	}
	
	/**
	 * Set Parameters Recursively
	 *
	 * @param array $array
	 * @param array $row
	 * @return void
	 */
	protected function setParametersRecursively($array, $row) {
		foreach ($array as $key => $value) {
			if ($key === 'field') {
				return $row[$value];
			}
			if (is_array($value)) {
				$array[$key] = $this->setParametersRecursively($value, $row);
			}
		}
		return $array;
	}
	
	/**
	 * Render Sitemap Content
	 *
	 * @return string
	 */
	protected function renderSitemapContent() {
		
		$xmlContent = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
		
		// foreach sitemap pages
		foreach($this->sitemap as $item) {
			
			// default change freq
			$changeFreq = $this->settings['xml_sitemap']['defaultChangeFreq'];
			
			// item page freq
			if($item['changeFreq'])
				$changeFreq = $item['changeFreq'];
			
			$xmlContent .= '<url>';
			$xmlContent .= '<loc>'.$this->getFrontendUri($item['pageUid'], $item['params']).'</loc>';
			$xmlContent .= '<lastmod>'.date('c', $item['timestamp']).'</lastmod>';
			$xmlContent .= '<changefreq>'.$changeFreq.'</changefreq>';
			$xmlContent .= '</url>';
		}
		
		$xmlContent .= PHP_EOL.'</urlset>';
		
		return $xmlContent;
	}
	
	/**
	 * Get Frontend Uri
	 *
	 * @param integer $pageUid
	 * @param array $additionalParams
	 * @return string
	 */
	protected function getFrontendUri($pageUid, array $additionalParams = array()) {
		
		// website baseurl
		$baseUrl = rtrim($GLOBALS['TSFE']->baseUrl, '/').'/';
		
		// get uri builder
		$uriBuilder = $this->controllerContext->getUriBuilder();

		$uri = $uriBuilder
		// set target page uid
		->setTargetPageUid($pageUid)
		// set use cache hash
		->setUseCacheHash($this->settings['xml_sitemap']['useCacheHash'])
		// set arguments
		->setArguments($additionalParams)
		// build
		->build();
			
		return htmlspecialchars(rawurldecode($baseUrl.ltrim($uri, '/')));
	}
}