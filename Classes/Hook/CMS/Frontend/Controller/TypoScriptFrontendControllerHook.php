<?php
namespace Netcreators\NcSiteessentials\Hook\CMS\Frontend\Controller;

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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * TypoScriptFrontendControllerHook
 */
class TypoScriptFrontendControllerHook
{

    /**
     * @var string $extKey
     */
    protected $extKey = 'NcSiteessentials';

    /**
     * @var array $settings
     */
    protected $settings = [];

    /**
     * @var string $content
     */
    protected $content = '';

    /**
     * construct
     */
    public function __construct()
    {
        // saves content
        $this->content = $this->getTypoScriptFrontendController()->content;

        // load typoscript configuration
        $this->settings = $this->loadTypoScriptConfiguration($this->extKey);
    }

    /**
     * Content Post Proc Output
     *
     * @return void
     */
    public function contentPostProcOutput()
    {
        if (
            // checks if rendering of front-end hyperlink attributes is enabled
            $this->settings['content']['contentPostProcOutput']['renderFrontendHyperlinkAttributes']
            // checks if the configuration of this method is available
            && $this->settings['renderFrontendHyperlinkAttributesConfig']
            // checks if the configuration of this method is of the type array
            && is_array($this->settings['renderFrontendHyperlinkAttributesConfig'])
        ) {

            $this->renderFrontendHyperlinkAttributes($this->settings['renderFrontendHyperlinkAttributesConfig']);
        }

        // remove meta generator tag
        if ($this->settings['content']['contentPostProcOutput']['metaGeneratorTag']['remove']) {

            $this->removeMetaGeneratorTag();

            // remove version number from meta generator tag
        } else {
            if ($this->settings['content']['contentPostProcOutput']['metaGeneratorTag']['removeVersionNumber']) {

                $this->removeVersionNumberFromMetaGeneratorTag();
            }
        }

        // returns the modified content
        $this->getTypoScriptFrontendController()->content = $this->content;
    }

    /**
     * Render Frontend Hyperlink Attributes
     *
     * @param array $config
     * @return void
     */
    public function renderFrontendHyperlinkAttributes($config)
    {
        // new DOMDocument
        $document = new \DOMDocument;

        // loads the html
        $document->loadHTML($this->content);

        // foreach all hyperlinks

        /** @var \DOMElement $hyperlink */
        foreach ($document->getElementsByTagName('a') as $hyperlink) {

            foreach ($config as $currentConfig) {

                // perform a preg_match on the href tag
                if ($currentConfig['pregMatchHrefTag']
                    // checks if the hyperlink has a href tag
                    && $hyperlink->hasAttribute('href')
                    // perform preg_match
                    && (trim($currentConfig['pregMatchHrefTag']) == '*' || preg_match(
                            $currentConfig['pregMatchHrefTag'],
                            $hyperlink->getAttribute('href')
                        ))
                ) {

                    // adds a class
                    if ($currentConfig['addClass']) {
                        $hyperlink->setAttribute(
                            'class',
                            ($hyperlink->hasAttribute('class') ? $hyperlink->getAttribute(
                                    'class'
                                ) . ' ' . $currentConfig['addClass'] : $currentConfig['addClass'])
                        );
                    }

                    // adds a attribute
                    if ($currentConfig['addAttribute'] && $currentConfig['addAttribute']['name'] && !$hyperlink->hasAttribute(
                            $currentConfig['addAttribute']['name']
                        )
                    ) {
                        $hyperlink->setAttribute(
                            $currentConfig['addAttribute']['name'],
                            $currentConfig['addAttribute']['value']
                        );
                    }
                }
            }
        }

        // check for setting dom document format output
        if ($this->settings['content']['domDocumentFormatOutput']) {

            $document->formatOutput = true;
        }

        // saves the html and pass it back in our content var
        $this->content = $document->saveHTML();
    }

    /**
     * Remove Meta Generator Tag
     *
     * @return void
     */
    public function removeMetaGeneratorTag()
    {
        // new DOMDocument
        $document = new \DOMDocument;

        // loads the html
        $document->loadHTML($this->content);

        // loop trough all metatags to find the meta generator tag

        /** @var \DOMElement $metatag */
        foreach ($metatags = $document->getElementsByTagName('meta') as $metatag) {

            if ($metatag->hasAttribute('name') && trim(strtolower($metatag->getAttribute('name'))) == 'generator') {

                $prent = $metatag->parentNode;
                $prent->replaceChild($document->createTextNode($metatag->nodeValue), $metatag);
            }
        }

        // check for setting dom document format output
        if ($this->settings['content']['domDocumentFormatOutput']) {

            $document->formatOutput = true;
        }

        // saves the html and pass it back in our content var
        $this->content = $document->saveHTML();
    }

    /**
     * Remove Version Number From Meta Generator Tag
     *
     * @return void
     */
    public function removeVersionNumberFromMetaGeneratorTag()
    {
        // new DOMDocument
        $document = new \DOMDocument;

        // loads the html
        $document->loadHTML($this->content);

        // loop trough all metatags to find the meta generator tag

        /** @var \DOMElement $metatag */
        foreach ($metatags = $document->getElementsByTagName('meta') as $metatag) {

            if ($metatag->hasAttribute('name') && trim(strtolower($metatag->getAttribute('name'))) == 'generator') {

                $metatag->setAttribute('content', $this->settings['__extSettings']['defaultMetaGeneratorVersion']);
            }
        }

        // check for setting dom document format output
        if ($this->settings['content']['domDocumentFormatOutput']) {

            $document->formatOutput = true;
        }

        // saves the html and pass it back in our content var
        $this->content = $document->saveHTML();
    }

    /**
     * Load TypoScript Configuration
     *
     * @param string $extKey
     * @return array
     */
    public function loadTypoScriptConfiguration($extKey)
    {

        // use the object/configuration manage to get the typoscript setup of $extKey
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            ObjectManager::class
        );

        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = $objectManager->get(
            ConfigurationManagerInterface::class
        );

        return $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            $extKey
        );
    }


    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

}