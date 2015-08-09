<?php
namespace TYPO3\NcSiteessentials\Domain\Model;


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

/**
 * Page
 */
class Page extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * abstract
	 * 
	 * @var string
	 */
	protected $abstract = '';
	
	/**
	 * description
	 * 
	 * @var string
	 */
	protected $description = '';
	
	/**
	 * keywords
	 * 
	 * @var string
	 */
	protected $keywords = '';
	
	/**
	 * author
	 * 
	 * @var string
	 */
	protected $author = '';
	
	/**
	 * author email
	 * 
	 * @var string
	 */
	protected $authorEmail = '';

	/**
	 * tstamp
	 * 
	 * @var integer
	 */
	protected $tstamp = 0;

	/**
	 * pid
	 * 
	 * @var integer
	 */
	protected $pid = 0;

	/**
	 * doktype
	 * 
	 * @var integer
	 */
	protected $doktype = 0;

	/**
	 * navHide
	 * 
	 * @var boolean
	 */
	protected $navHide = FALSE;

	/**
	 * excludeInSitemap
	 * 
	 * @var boolean
	 */
	protected $excludeInSitemap = FALSE;

	/**
	 * changeFreq
	 * 
	 * @var string
	 */
	protected $changeFreq = '';
	
	/**
	 * Tracking Code
	 *
	 * @var string
	 */
	protected $trackingCode;
	
	/**
	 * Returns the abstract
	 * 
	 * @return integer $abstract
	 */
	public function getAbstract() {
		return $this->abstract;
	}

	/**
	 * Sets the abstract
	 * 
	 * @param integer $abstract
	 * @return void
	 */
	public function setAbstract($abstract) {
		$this->abstract = $abstract;
	}
	
	/**
	 * Returns the description
	 * 
	 * @return integer $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 * 
	 * @param integer $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * Returns the keywords
	 * 
	 * @return integer $keywords
	 */
	public function getKeywords() {
		return $this->keywords;
	}

	/**
	 * Sets the keywords
	 * 
	 * @param integer $keywords
	 * @return void
	 */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}
	
	/**
	 * Returns the author
	 * 
	 * @return integer $author
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * Sets the author
	 * 
	 * @param integer $author
	 * @return void
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}
	
	/**
	 * Returns the author email
	 * 
	 * @return integer $authorEmail
	 */
	public function getAuthorEmail() {
		return $this->authorEmail;
	}

	/**
	 * Sets the author email
	 * 
	 * @param integer $authorEmail
	 * @return void
	 */
	public function setAuthorEmail($authorEmail) {
		$this->authorEmail = $authorEmail;
	}

	/**
	 * Returns the tstamp
	 * 
	 * @return integer $tstamp
	 */
	public function getTstamp() {
		return $this->tstamp;
	}

	/**
	 * Sets the tstamp
	 * 
	 * @param integer $tstamp
	 * @return void
	 */
	public function setTstamp($tstamp) {
		$this->tstamp = $tstamp;
	}

	/**
	 * Returns the pid
	 * 
	 * @return integer $pid
	 */
	public function getPid() {
		return $this->pid;
	}

	/**
	 * Sets the pid
	 * 
	 * @param integer $pid
	 * @return void
	 */
	public function setPid($pid) {
		$this->pid = $pid;
	}

	/**
	 * Returns the doktype
	 * 
	 * @return integer $doktype
	 */
	public function getDoktype() {
		return $this->doktype;
	}

	/**
	 * Sets the doktype
	 * 
	 * @param integer $doktype
	 * @return void
	 */
	public function setDoktype($doktype) {
		$this->doktype = $doktype;
	}

	/**
	 * Returns the navHide
	 * 
	 * @return boolean $navHide
	 */
	public function getNavHide() {
		return $this->navHide;
	}

	/**
	 * Sets the navHide
	 * 
	 * @param boolean $navHide
	 * @return void
	 */
	public function setNavHide($navHide) {
		$this->navHide = $navHide;
	}

	/**
	 * Returns the boolean state of navHide
	 * 
	 * @return boolean
	 */
	public function isNavHide() {
		return $this->navHide;
	}

	/**
	 * Returns the excludeInSitemap
	 * 
	 * @return boolean $excludeInSitemap
	 */
	public function getExcludeInSitemap() {
		return $this->excludeInSitemap;
	}

	/**
	 * Sets the excludeInSitemap
	 * 
	 * @param boolean $excludeInSitemap
	 * @return void
	 */
	public function setExcludeInSitemap($excludeInSitemap) {
		$this->excludeInSitemap = $excludeInSitemap;
	}

	/**
	 * Returns the boolean state of excludeInSitemap
	 * 
	 * @return boolean
	 */
	public function isExcludeInSitemap() {
		return $this->excludeInSitemap;
	}

	/**
	 * Returns the changeFreq
	 * 
	 * @return string $changeFreq
	 */
	public function getChangeFreq() {
		return $this->changeFreq;
	}

	/**
	 * Sets the changeFreq
	 * 
	 * @param string $changeFreq
	 * @return void
	 */
	public function setChangeFreq($changeFreq) {
		$this->changeFreq = $changeFreq;
	}
	
	/**
	 * Returns the tracking code
	 *
	 * @param string $trackingCode
	 * @return void
	 */
	public function setTrackingCode($trackingCode) {
		$this->trackingCode = $trackingCode;
	}

	/**
	 * Returns the tracking code
	 *
	 * @param string $trackingCode
	 * @return void
	 */
	public function getTrackingCode() {
		return $this->trackingCode;
	}

}