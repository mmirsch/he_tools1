<?php
class he_tools_db  {
static public $sixSql;
static public $sixDB;
static public $typo3LogSql;
static public $typo3LogDB;

	public static function persdbConnect() {
		$db = pg_connect("host=postgres2.hs-esslingen.de dbname=infopgdb user=mirsch password=Adinjoc") or die('Could not connect to mysql server.' );
		return $db;
	}
	
	public static function initDb() {
		if (!(self::$sixSql instanceof t3lib_db)) {
		  self::$sixSql = t3lib_div::makeInstance('t3lib_db');
			self::$sixDB = self::$sixSql->sql_pconnect("rzlx2000.hs-esslingen.de","rzlx33xxtypo3","v7L4uJ3X2t2RRbpS") or die('Could not connect to SixCMS-Mysql server.' );
			self::$sixSql->sql_select_db("sixcms6_fhte",self::$sixDB) or die('Could not select database.');
		}
	}
	
	public static function typo3Logs_Connect() {
	  self::$typo3LogSql = t3lib_div::makeInstance('t3lib_db');
		self::$typo3LogDB = self::$typo3LogSql->sql_pconnect('mytypo3db.hs-esslingen.de','rzlx33xxtypo3','v7L4uJ3X2t2RRbpS') or die('Could not connect to TYPO3-Logfiles server.' );
//		self::$typo3LogSql->sql_select_db('t3logs',self::$typo3LogDB) or die('Could not select database.');
	}

	public static function typo3Logs_sql_query($query) {
    return self::$typo3LogSql->sql_query($query);
	}
	
	public static function typo3Logs_disconnect() {
    return self::$typo3LogSql->connectDB();
	}
	
	public static function typo3Logs_fetch_assoc($abfrage) {
		return self::$typo3LogSql->sql_fetch_assoc($abfrage);
	}
	
	public static function sixCmsConnect() {
	  self::$sixSql = t3lib_div::makeInstance('t3lib_db');
		self::$sixDB = self::$sixSql->sql_pconnect("rzlx2000.hs-esslingen.de","rzlx33xxtypo3","v7L4uJ3X2t2RRbpS") or die('Could not connect to SixCMS-Mysql server.' );
		self::$sixSql->sql_select_db("sixcms6_fhte",self::$sixDB) or die('Could not select database.');
	}

	public static function six_sql_query($query) {
		self::initDb();
    return self::$sixSql->sql_query($query);
	}
	
	public static function six_sql_fetch_assoc($abfrage) {
		self::initDb();
		return self::$sixSql->sql_fetch_assoc($abfrage);
	}
	
	public static function six_sql_fetch_row($abfrage) {
		self::initDb();
		return self::$sixSql->sql_fetch_row($abfrage);
	}
	
	public static function six_sql_num_rows($abfrage) {
		self::initDb();
		return self::$sixSql->sql_num_rows($abfrage);
	}
	
	public static function six_disconnect() {
    return self::$sixSql->connectDB();
	}


	public static function mysql2Connect() {
	  $mysql2Sql = t3lib_div::makeInstance('t3lib_db');
		$mysql2Sql->sql_pconnect("mysql2.hs-esslingen.de","rzlx33xxtypo3_ro","Neuhioceid3") or die('Could not connect to Mysql server "mysql2.hs-esslingen.de".' );
		$mysql2Sql->sql_select_db("wetterstation") or die('Could not select database "wetterstation".');
		return $mysql2Sql;
	}

}
?>
