CREATE TABLE pages (
	he_pagetype tinytext,
	he_suchbegriffe tinytext
);

CREATE TABLE be_users (
	tx_hetools_lesezeichen longtext,
	KEY username (username),
	KEY email (email)
);

CREATE TABLE tt_news (
	tx_hetools_titel_startseite tinytext,
	tx_hetools_kandidat_arbeit tinytext,
	tx_hetools_sortierfeld tinytext
);

CREATE TABLE tx_he_zeitschriftenliste (
  uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	titel tinytext,
	sortiertitel varchar(255) default NULL,
	signatur tinytext NOT NULL,
	bestandsnachweis tinytext NOT NULL,
 	PRIMARY KEY (uid),
 	KEY parent (pid),
 	KEY sortiertitel (sortiertitel)
);

CREATE TABLE tt_content (
	select_key varchar(150) default '',
	tx_hetools_filelist_sortierfeld tinytext,
	tx_hetools_filelist_dateitypen tinytext,
	tx_hetools_filelist_sortierung tinytext,
	tx_hetools_filelist_layout tinytext
);

CREATE TABLE tx_dam (
	tx_hetools_dam_sortiernummer tinytext
);

CREATE TABLE tx_irfaq_q (
	tx_hetools_max_words int(11) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_hetools_wmw_abteilungen'
#
CREATE TABLE tx_hetools_wmw_abteilungen (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_wer_macht_was'
#
CREATE TABLE tx_hetools_wer_macht_was (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	title tinytext,
	beschreibung tinytext,
	abteilungen tinytext,
	personen tinytext,
	linkadresse tinytext,
	Linkbezeichnung tinytext,
	datei  tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_kategorie1'
#
CREATE TABLE tx_hetools_kategorie1 (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_hetools_kategorie2'
#
CREATE TABLE tx_hetools_kategorie2 (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_bereich'
#

CREATE TABLE tx_hetools_bereich (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	kategorie1 tinytext,
	kategorie2 tinytext,
	title tinytext,
	telefon tinytext,
	email tinytext,
	bemerkung longtext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_he_standorte'
#
CREATE TABLE tx_he_standorte (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	title varchar(80) DEFAULT '' NOT NULL,
	title_en varchar(80) DEFAULT '' NOT NULL,
	kuerzel varchar(3) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_he_fakultaeten'
#
CREATE TABLE tx_he_fakultaeten (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	title varchar(80) DEFAULT '' NOT NULL,
	title_en varchar(80) DEFAULT '' NOT NULL,
	kuerzel varchar(3) DEFAULT '' NOT NULL,
	standort int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_he_studiengaenge'
#
CREATE TABLE tx_he_studiengaenge (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	title_en varchar(255) DEFAULT '' NOT NULL,
	kuerzel varchar(4) DEFAULT '' NOT NULL,
	fakultaet int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_he_modules_en'
#
CREATE TABLE tx_he_modules_en (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(1) DEFAULT '0' NOT NULL,
	deleted tinyint(1) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	
	title varchar(255) DEFAULT '' NOT NULL,
	zusatz varchar(255) DEFAULT '' NOT NULL,
	campus int(11) DEFAULT '0' NOT NULL,
	fakultaet int(11) DEFAULT '0' NOT NULL,
	studiengang varchar(255) DEFAULT '' NOT NULL,
	verantwortliche text,
	download varchar(255) DEFAULT '' NOT NULL,
	link varchar(255) DEFAULT '' NOT NULL,
	credits tinyint(4) DEFAULT '0' NOT NULL,
	level tinyint(4) DEFAULT '0' NOT NULL,
	semester tinyint(4) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_module_studiengaenge'
#
CREATE TABLE tx_hetools_module_studiengaenge (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	title tinytext,
	title_en tinytext,
	fakultaet tinytext,
	schwerpunkt tinytext,
	lsf_stdg tinytext,
	lsf_abs tinytext,
	abschluss tinytext,
	sem_schwp tinyint(4) DEFAULT '4' NOT NULL,
	six_id tinytext,
	six_id_handbuch tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_module_vertiefungen'
#
CREATE TABLE tx_hetools_module_vertiefungen (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	modstud_id int(11) DEFAULT '0' NOT NULL,
	vertiefung  varchar(255) default NULL,
	kuerzel  varchar(255) default NULL,
	version tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_hetools_veranstaltungen'
#
CREATE TABLE tx_hetools_veranstaltungen (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(1) DEFAULT '0' NOT NULL,
	deleted tinyint(1) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	title tinytext,
	max_teilnehmer int(11) DEFAULT '0' NOT NULL,
	ort tinytext,
	raum tinytext,
	link tinytext,
	datum int(11) DEFAULT '0' NOT NULL,
	startzeit tinytext,
	endzeit tinytext,
	intervall int(11) DEFAULT '0' NOT NULL,
	pause int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_veranstaltungen_termine'
#
CREATE TABLE tx_hetools_veranstaltungen_termine (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(1) DEFAULT '0' NOT NULL,
	deleted tinyint(1) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	veranstaltung int(11) DEFAULT '0' NOT NULL,
	von tinytext,
	bis tinytext,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_veranstaltungen_belegung'
#
CREATE TABLE tx_hetools_veranstaltungen_belegung (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(1) DEFAULT '0' NOT NULL,
	deleted tinyint(1) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	termin int(11) DEFAULT '0' NOT NULL,
	username text,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_veranstaltungen_abhaengigkeiten'
#
CREATE TABLE tx_hetools_veranstaltungen_abhaengigkeiten (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(1) DEFAULT '0' NOT NULL,
	deleted tinyint(1) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	title text,
	veranstaltungen text,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_solr_submitted_disabled_pages'
#
CREATE TABLE tx_hetools_solr_submitted_disabled_pages (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	pageId int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_veranstaltungen_belegung'
#
CREATE TABLE tx_hetools_mensa (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	datum int(11) DEFAULT '0' NOT NULL,
	tagesplan longtext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_sb_online_anfragen'
#
CREATE TABLE tx_hetools_sb_online_anfragen (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	thema text,
	themaId text,
	zielgruppe text,
	anfrage longtext,
	username text,
	original_id int(11) DEFAULT '0' NOT NULL,
	nr int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_antrage_gastkennungen'
#
CREATE TABLE tx_hetools_antrage_gastkennungen (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	bereich text,
	veranstaltung text,
	ende tinytext,
	username tinytext,
	kennungen longtext,
	kennungen_angelegt tinyint(4) DEFAULT '0' NOT NULL,
	csv_exportiert tinyint(4) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_hetools_einfuehrungsveranstaltungen'
#
CREATE TABLE tx_hetools_einfuehrungsveranstaltungen (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	datum int(11) DEFAULT '0' NOT NULL,
	beginn tinytext,
	ende tinytext,
	raum text,
	dozent text,
	standort text,
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_hetools_anmeldungen_einfuehrungsveranstaltungen'
#
CREATE TABLE tx_hetools_anmeldungen_einfuehrungsveranstaltungen (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	veranstaltung int(11) DEFAULT '0' NOT NULL,
	vorname text,
	nachname text,
	matrikelnummer text,
	teilnehmernummer text,
	email text,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
############## Infoscreen ##############
#

#
# Table structure for table 'tx_hetools_infoscreen_elemente'
#
CREATE TABLE tx_hetools_infoscreen_elemente (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title text,
	beschreibung text,
	bild text,
	raum text,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_infoscreen_anzeige_zeitraeume'
#
CREATE TABLE tx_hetools_infoscreen_anzeige_zeitraeume (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	von int(11) DEFAULT '0' NOT NULL,
	bis int(11) DEFAULT '0' NOT NULL,
	anzeigetyp tinytext,
	anzeigeObjekt int(11) DEFAULT '0' NOT NULL,
	kalenderKategorie int(11) DEFAULT '0' NOT NULL,
	inhaltsElement int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hetools_infoscreen_redirects'
#
CREATE TABLE tx_hetools_infoscreen_redirects (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	ip varchar(40) DEFAULT NULL,
	redirect_url varchar(255) DEFAULT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
############## Bestellwesen ##############
#

#
# Table structure for table 'tx_hebest_hauptkategorie'
#
CREATE TABLE tx_hebest_hauptkategorie (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_hebest_unterkategorie'
#
CREATE TABLE tx_hebest_unterkategorie (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_hebest_eigenschaft1'
#
CREATE TABLE tx_hebest_eigenschaft1 (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hebest_eigenschaft2'
#
CREATE TABLE tx_hebest_eigenschaft2 (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_hebest_hersteller'
#
CREATE TABLE tx_hebest_hersteller (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	keyword1 int(11) DEFAULT '0' NOT NULL,
	keyword2 int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	anschrift tinytext,
	plz tinytext,
	stadt tinytext,
	tel tinytext,
	fax tinytext,
	www tinytext,
	email tinytext,
	bemerkung longtext,
	interne_bemerkung longtext,
	bild  tinytext,
	intranet tinyint(4) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hebest_lieferanten'
#
CREATE TABLE tx_hebest_lieferanten (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	keyword1 int(11) DEFAULT '0' NOT NULL,
	keyword2 int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	anschrift tinytext,
	plz tinytext,
	stadt tinytext,
	tel tinytext,
	fax tinytext,
	www tinytext,
	email tinytext,
	bemerkung longtext,
	interne_bemerkung longtext,
	bild  tinytext,
	intranet tinyint(4) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_hebest_stadt'
#
CREATE TABLE tx_hebest_stadt (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hebest_produktname'
#
CREATE TABLE tx_hebest_produktname (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_hebest_keyword1'
#
CREATE TABLE tx_hebest_keyword1 (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_hebest_keyword2'
#
CREATE TABLE tx_hebest_keyword2 (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hebest_edv_checklist'
#
CREATE TABLE tx_hebest_edv_checklist (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	hauptkategorie int(11) DEFAULT '0' NOT NULL,
	unterkategorie int(11) DEFAULT '0' NOT NULL,
	eigenschaft1 int(11) DEFAULT '0' NOT NULL,
	eigenschaft2 int(11) DEFAULT '0' NOT NULL,
	hersteller int(11) DEFAULT '0' NOT NULL,
	lieferant int(11) DEFAULT '0' NOT NULL,
	produktname tinytext,
	anzeigen_bis int(11) DEFAULT '0' NOT NULL,
	ansprechpartner tinytext,
	bemerkung longtext,
	interne_bemerkung longtext,
	link  tinytext,
	linktext  tinytext,
	bild  tinytext,
	intranet tinyint(4) DEFAULT '0' NOT NULL,
	oeffentlich_verbergen tinyint(4) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hebest_artikel'
#
CREATE TABLE tx_hebest_artikel (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	hauptkategorie int(11) DEFAULT '0' NOT NULL,
	unterkategorie int(11) DEFAULT '0' NOT NULL,
	eigenschaft1 int(11) DEFAULT '0' NOT NULL,
	eigenschaft2 int(11) DEFAULT '0' NOT NULL,
	hersteller int(11) DEFAULT '0' NOT NULL,
	lieferant int(11) DEFAULT '0' NOT NULL,
	produktname tinytext,
	artikelnummer tinytext,
	anzeigen_bis int(11) DEFAULT '0' NOT NULL,
	ansprechpartner tinytext,
	bemerkung longtext,
	interne_bemerkung longtext,
	link  tinytext,
	linktext  tinytext,
	bild  tinytext,
	intranet tinyint(4) DEFAULT '0' NOT NULL,
	oeffentlich_verbergen tinyint(4) DEFAULT '0' NOT NULL,
	preis  tinytext,
	hersteller_bezeichnung tinytext,
	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for table 'tx_hebest_hauptkategorie'
#
CREATE TABLE tx_hebest_hauptkategorie (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_hebest_unterkategorie'
#
CREATE TABLE tx_hebest_unterkategorie (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);


CREATE TABLE tx_he_qr_info (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,
  tstamp int(11) DEFAULT '0' NOT NULL,
  crdate int(11) DEFAULT '0' NOT NULL,
  cruser_id int(11) DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  qr_id varchar(20) DEFAULT '' NOT NULL,
  dp_key varchar(39) DEFAULT '' NOT NULL,
  klartext varchar(30) DEFAULT '' NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid)
);
