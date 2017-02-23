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
 * GoogleAnalyticsController
 */
class GoogleAnalyticsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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

        $rootPage = $this->pageRepository->findOneByUid($this->getTypoScriptFrontendController()->rootLine[0]['uid']);
        if ($rootPage) {

            $this->getTypoScriptFrontendController(
            )->additionalHeaderData['nc_siteessentials_googleAnalyticsTrackingCode']
                = (string)$rootPage->getTrackingCode();
        }

        // prevent fluid template rendering.
        $this->view = null;

        return;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}