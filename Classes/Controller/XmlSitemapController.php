<?php
namespace Netcreators\NcSiteessentials\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Arek van Schaijk <arek@netcreators.nl>, Netcreators
 *  (c) 2017 Leonie Philine Bitto <extensions@netcreators.nl>, Netcreators
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
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * XmlSitemapController
 */
class XmlSitemapController extends XmlSitemapBaseController
{

    /**
     * pageRepository
     *
     * @var \Netcreators\NcSiteessentials\Domain\Repository\PageRepository
     * @inject
     */
    protected $pageRepository = null;

    /**
     * action render
     *
     * @return void
     */
    public function renderAction()
    {

        // render allowed doktype array
        $this->renderAllowedDoktypeArray();

        // find all pages by settings
        $pages = $this->pageRepository->findAllPagesBySettings($this->settings);

        // render pages relation array
        $this->renderPagesRelationArray($pages);

        // make change freq recursively
        if ($this->settings['xml_sitemap']['makeChangeFreqRecursively']) {
            $this->makeChangeFreqRecursively($this->getTypoScriptFrontendController()->rootLine[0]['uid']);
        }

        $id = $this->getTypoScriptFrontendController()->id;

        // resolve original shortcut page UID, in cases where the site root page is a shortcut to its first subpage.
        // if we do not resolve the original shortcut page UID, we will always only collect a sitemap for that first subpage,
        // instead of for the site root.
        if ($this->isCurrentPageShortcutTargetOfSiteRootPage()) {
            $id = (int)$this->getTypoScriptFrontendController()->rootLine[0]['uid'];
        }

        // insert page and subpages in sitemap
        $this->insertPageAndSubpagesInSitemap($id);

        // insert custom mapped pages in sitemap
        $this->insertCustomMappedPagesInSitemap($id);

        /**
         * -- TEMPORARY FIX --
         * It seems that TYPO3 6.2 doesn't render xml output well
         * Reported: https://forge.typo3.org/issues/63899
         */
        header('Content-Type: application/xml; charset=utf-8');
        echo $this->renderSitemapContent();
        exit;
    }

    /**
     * Check if:
     *    - The current page is a direct child of the site root page.
     *    - The site root page is a shortcut.
     *    - The shortcut target is the current page.
     *
     * If the site root page is a shortcut to one of its sub-pages, we would not be able to
     * ever create a SiteMap for the entire site. If requested as "/sitemap.xml", you would still
     * receive a SiteMap containing only the sub-pages of the shortcut target.
     *
     * This would be easier to achieve if TypoScriptFrontendController::$originalShortcutPage wasn't protected:
     *
     *    if($this->getTypoScriptFrontendController()->originalShortcutPage) { // <-- No access.
     *        $id = $this->getTypoScriptFrontendController()->originalShortcutPage['uid'];
     *    }
     *
     * @return bool
     */
    protected function isCurrentPageShortcutTargetOfSiteRootPage()
    {
        // RootLine needs to consist of current page and site root page only.
        if (count($this->getTypoScriptFrontendController()->rootLine) != 2) {
            return false;
        }

        // If site root page is not a shortcut, we can quit here.
        if ($this->getTypoScriptFrontendController()->rootLine[0]['doktype']
            != \TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_SHORTCUT
        ) {
            return false;
        }

        // Get complete site root page record.
        // 	(Rootline only contains \TYPO3\CMS\Core\Utility\RootlineUtility::$rootlineFields.
        //   Shortcut fields are excluded.)
        $siteRootPage = $this->getTypoScriptFrontendController()->sys_page->getPage(
            $this->getTypoScriptFrontendController()->rootLine[0]['uid']
        );
        $shortcutPage = $this->getTypoScriptFrontendController()->getPageShortcut(
            $siteRootPage['shortcut'],
            $siteRootPage['shortcut_mode'],
            $siteRootPage['uid']
        );

        return $shortcutPage['uid'] == $this->getTypoScriptFrontendController()->id;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
