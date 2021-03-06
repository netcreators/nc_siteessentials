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
 * MetatagController
 */
class MetatagController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * HeaderContent
     *
     * @param string $headerContent
     */
    protected $headerContent = '';

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
        // checks if the metatags are enabled
        if (!$this->settings['metatags']['enabled']) {
            return;
        }

        // get current page
        $page = $this->pageRepository->findOneByUid($this->getTypoScriptFrontendController()->id);

        // meta description
        if ($metaDescription = $this->trimAndRemoveNewLines($page->getDescription())) {
            $this->addMetaTag('description', null, $metaDescription);
        }

        // meta keywords
        if ($metaKeywords = $this->trimAndRemoveNewLines($page->getKeywords())) {
            $this->addMetaTag('keywords', null, $metaKeywords);
        }

        // meta abstract
        if ($metaAbstract = $this->trimAndRemoveNewLines($page->getAbstract())) {
            $this->addMetaTag('abstract', null, $metaAbstract);
        }

        // meta author
        if ($metaAuthor = $this->trimAndRemoveNewLines($page->getAuthor())) {

            $authorEmail = null;
            // meta author email
            if ($this->settings['metatags']['renderAuthorEmail'] && $page->getAuthorEmail()) {
                $authorEmail = ', ' . ($this->settings['metatags']['replaceAtSign'] ? str_replace(
                        '@',
                        $this->settings['metatags']['replaceAtSign'],
                        $page->getAuthorEmail()
                    ) : $page->getAuthorEmail());
            }

            $this->addMetaTag('author', null, $metaAuthor . $authorEmail);
        }

        // meta robots
        if ($this->settings['metatags']['alternativeTags']['robots']) {
            $this->addMetaTag('robots', null, $this->settings['metatags']['alternativeTags']['robots']);
        }

        // meta copyright
        if ($this->settings['metatags']['alternativeTags']['copyright']) {
            $this->addMetaTag('copyright', null, $this->settings['metatags']['alternativeTags']['copyright']);
        }

        // meta content-language
        if ($this->settings['metatags']['alternativeTags']['contentLanguage']) {
            $this->addMetaTag(
                null,
                'content-language',
                $this->settings['metatags']['alternativeTags']['contentLanguage']
            );
        }

        // meta distribution
        if ($this->settings['metatags']['alternativeTags']['distribution']) {
            $this->addMetaTag('distribution', null, $this->settings['metatags']['alternativeTags']['distribution']);
        }

        // meta revisit-after
        if ($this->settings['metatags']['alternativeTags']['revisitAfter']) {
            $this->addMetaTag(
                'revisit-after',
                null,
                $this->settings['metatags']['alternativeTags']['revisitAfter']
            );
        }

        // render header content
        $this->getTypoScriptFrontendController()->additionalHeaderData['nc_siteessentials_metaTags']
            = $this->headerContent;

        // prevent fluid template rendering.
        $this->view = null;
    }

    /**
     * Trim And Remove New Lines
     *
     * @param string $string
     * @return string
     */
    public function trimAndRemoveNewLines($string)
    {
        return trim(preg_replace('/\s\s+/', ' ', $string));
    }

    /**
     * Add Meta Tag
     *
     * @param string $name
     * @param string $httpEquiv
     * @param $content
     * @return void
     */
    public function addMetaTag($name = null, $httpEquiv, $content)
    {
        $this->headerContent .= '<meta ' . ($name ? 'name="' . $name . '"' : '')
            . ($httpEquiv ? 'http-equiv="' . $httpEquiv . '"' : '')
            . ' content="' . $content . '" />' . PHP_EOL;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}