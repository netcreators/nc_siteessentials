#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_ncsiteessentials_xmlsitemap_exclude tinyint(4) unsigned DEFAULT '0' NOT NULL,
	tx_ncsiteessentials_xmlsitemap_changefreq varchar(255) DEFAULT '' NOT NULL,
	tx_ncsiteessentials_robotstxt_content text NOT NULL,
	tx_ncsiteessentials_googleanalytics_content text NOT NULL
);