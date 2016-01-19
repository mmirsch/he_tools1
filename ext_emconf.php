<?php

########################################################################
# Extension Manager/Repository config file for ext "he_tools".
#
# Auto generated 29-08-2012 16:34
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'HE Tools',
	'description' => 'Verschiedene Backend-Module und Frontend-Plugins:',
	'category' => 'misc',
	'author' => 'Manfred Mirsch',
	'author_email' => 'Manfred.Mirsch@hs-esslingen.de',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/wer_macht_was',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:79:{s:9:"ChangeLog";s:4:"a16b";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"5501";s:17:"ext_localconf.php";s:4:"bd43";s:14:"ext_tables.php";s:4:"ee40";s:14:"ext_tables.sql";s:4:"a87c";s:15:"flexform_ds.xml";s:4:"e82d";s:14:"icon_tools.gif";s:4:"5501";s:16:"locallang_db.xml";s:4:"c004";s:7:"tca.php";s:4:"e604";s:19:"doc/wizard_form.dat";s:4:"a8dd";s:20:"doc/wizard_form.html";s:4:"3ccd";s:29:"eid/class.tx_he_tools_eid.php";s:4:"bdcf";s:31:"hooks/class.tx_browserhooks.php";s:4:"a0f3";s:43:"hooks/class.tx_he_tools_clearcache_hook.php";s:4:"5cf0";s:36:"hooks/class.tx_he_tools_damhooks.php";s:4:"c0b3";s:38:"hooks/class.tx_he_tools_news_hooks.php";s:4:"6ec7";s:43:"hooks/class.tx_he_tools_powermail_hooks.php";s:4:"90cd";s:38:"hooks/class.tx_he_tools_solr_hooks.php";s:4:"6cca";s:45:"hooks/class.tx_he_tools_templavoila_hooks.php";s:4:"3407";s:44:"hooks/class.tx_he_tools_wt_gallery_hooks.php";s:4:"6412";s:33:"lang/locallang_csh_tt_content.php";s:4:"1a54";s:15:"lib/XML.inc.php";s:4:"2315";s:33:"lib/class.tx_he_tools_a_bis_z.php";s:4:"f4fe";s:40:"lib/class.tx_he_tools_be_mountpoints.php";s:4:"1b30";s:35:"lib/class.tx_he_tools_calexport.php";s:4:"f4c6";s:28:"lib/class.tx_he_tools_db.php";s:4:"93c0";s:36:"lib/class.tx_he_tools_infoscreen.php";s:4:"5616";s:38:"lib/class.tx_he_tools_lib_db_suche.php";s:4:"0af5";s:29:"lib/class.tx_he_tools_lsf.php";s:4:"7724";s:32:"lib/class.tx_he_tools_module.php";s:4:"dc80";s:35:"lib/class.tx_he_tools_powermail.php";s:4:"a74d";s:36:"lib/class.tx_he_tools_rz_skripte.php";s:4:"d9ad";s:30:"lib/class.tx_he_tools_solr.php";s:4:"54ae";s:40:"lib/class.tx_he_tools_suchergebnisse.php";s:4:"c5ab";s:30:"lib/class.tx_he_tools_util.php";s:4:"1813";s:48:"lib/class.tx_he_tools_veranstaltungs_buchung.php";s:4:"b69a";s:45:"lib/class.tx_he_tools_zeitschriftenlisten.php";s:4:"a5d4";s:15:"lib/globals.php";s:4:"9c4e";s:12:"lib/test.php";s:4:"619d";s:25:"lib/sixcms/Decorators.php";s:4:"9bd9";s:21:"lib/sixcms/States.php";s:4:"a0fc";s:29:"lib/sixcms/class.HTMLSax3.php";s:4:"2571";s:30:"lib/sixcms/class.MyHandler.php";s:4:"e045";s:25:"lib/sixcms/class.curl.php";s:4:"b62e";s:28:"lib/sixcms/class.myIconv.php";s:4:"9af4";s:37:"lib/tmp/class.tx_he_tools_a_bis_z.php";s:4:"f4fe";s:30:"mod1/class.he_backend_util.php";s:4:"d3a7";s:23:"mod1/class.sys_logs.php";s:4:"7d75";s:32:"mod1/class.tx_he_tools_alias.php";s:4:"d5c8";s:42:"mod1/class.tx_he_tools_pers_verwaltung.php";s:4:"6ab0";s:13:"mod1/conf.php";s:4:"6954";s:19:"mod1/icon_tools.gif";s:4:"5501";s:14:"mod1/index.php";s:4:"4b4c";s:18:"mod1/locallang.xml";s:4:"2efe";s:22:"mod1/locallang_mod.xml";s:4:"d3c7";s:19:"mod1/moduleicon.gif";s:4:"8074";s:33:"mod1/six_util/class.he_module.php";s:4:"2f77";s:38:"mod1/six_util/class.he_werMachtWas.php";s:4:"78c6";s:28:"pi1/class.tx_hetools_pi1.php";s:4:"e1d5";s:17:"pi1/locallang.xml";s:4:"4b02";s:17:"res/bereiche.tmpl";s:4:"f876";s:12:"res/book.gif";s:4:"8b87";s:16:"res/he_tools.css";s:4:"acc5";s:22:"res/hochschule_a_z.css";s:4:"26e7";s:23:"res/jquery-1.7.2.min.js";s:4:"b8d6";s:34:"res/jquery-ui-1.8.19.custom.min.js";s:4:"e1fb";s:26:"res/jquery.bxSlider.min.js";s:4:"ea1e";s:24:"res/jquery.easing.1.3.js";s:4:"6516";s:25:"res/locallang_csh_ttc.xml";s:4:"4ada";s:16:"res/template.tpl";s:4:"544d";s:23:"res/veranstaltungen.css";s:4:"700f";s:16:"res/video-js.swf";s:4:"b7a8";s:16:"res/video.min.js";s:4:"de24";s:21:"res/zeitschriften.css";s:4:"4b31";s:18:"static/bereiche.ts";s:4:"59a0";s:20:"static/icon_plus.gif";s:4:"fd84";s:42:"user/class.user_hePageNotFoundhandling.php";s:4:"6d56";s:17:"user/redirect.php";s:4:"1b0b";}',
	'suggests' => array(
	),
);

?>