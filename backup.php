<?php
// #########################################
// qs-backupper by 久鶻堂 <dev@q.kostu.do>
// ※ php 5.x でも動くように書いてる
// #########################################

// #######################
// 設定系
// #######################
$strVersion		= "0.1a - 20190208";

// デバッグフラグ
$flgDebug			= false;

// プロファイルの入れ物
$arrProfile			= array();

// 特定プロファイルの実行判定用
$strProfile			= "";

// 引数の入れ物
$arrArg				= array();


// #######################
// 初期化系
// #######################

// classロード
require_once( "./class/base.php" );
require_once( "./class/profile.php" );
require_once( "./class/driver/base.php" );
require_once( "./class/driver/dir.php" );
require_once( "./class/driver/mysql.php" );
require_once( "./class/report/base.php" );
require_once( "./class/report/mail.php" );

// プロファイルロード
if ( !( $objDir = opendir( "./profile" ) ) ) {
	die( "cloud not open profile dir." );

} else {
	while ( $strProfile = readdir( $objDir ) ) {
		// *.profile.php を include ( sample.profile.php は除外 )
		if ( preg_match( "/^(.+)\.profile\.php$/", $strProfile ) && $strProfile != "sample.profile.php" ) {
			include_once( "./profile/{$strProfile}" );
		}
	}
}

// 引数展開
if ( $argc > 0 ) {
	// 受け付けるオプション
	$arrArgAccept	= array(
					"-p"	=> "profile",
			"--profile"	=> "profile"
		);
	$strArgCurrent	= "";

	foreach( $argv as $strArg ){
		// 判別指定の場合
		if ( $strArgCurrent != "" ) {
			switch ( $strArgCurrent ) {
				case "profile":
					// オプション値空判定
					if ( $strArg == "" ) {
						die( "Enter profile name to run.\n" );
						
					// プロファイルオプション多重指定判定
					} elseif ( $strProfile != "" ) {
						die( "Only one profile could be specified.\n" );

					// プロファイル不在判定
					} elseif ( !isset( $arrProfile[ $strArg ] ) ) {
						die( "No such profile: {$strArg}\n" );

					}

					// OKなら指定プロファイルに代入
					$strProfile	= $strArg;
					break;
			}
			$strArgCurrent	= "";

		// デバッグモード判別
		} elseif ( $strArg == "-d" || $strArg == "--debug" ) {
			$flgDebug	= true;

		// バージョン出力
		} elseif ( $strArg == "-v" || $strArg == "--version" ) {
			echo "qs-backupper {$strVersion}, Copyright (C) 2019 qkotsudo\n";
			echo "GitHub: https://github.com/qkotsudo/qs-backupper\n";
			exit;

		// ヘルプモード判別
		} elseif ( $strArg == "-h" || $strArg == "--help" ) {
			echo "Usage: php backup.php [options]\n";
			echo "\n";
			echo "  --debug, -d   Enable debug Mode.\n";
			echo "  --version, -v Print version and exit successfully.\n";
			echo "  --help, -h    Print this help and exit successfully.\n";
			echo "  --profile, -p Specify profile to run.\n";
			echo "\n";
			exit;

		// プロファイルオプション判別
		} elseif ( isset( $arrArgAccept[ $strArg ] ) ) {
			$strArgCurrent	= $arrArgAccept[ $strArg ];
		}
	}
}

if ( count( $arrProfile ) == 0 ) {
	echo "Nothing to do.\n";
	exit;
}


// #######################
// 処理系
// #######################
$flgOK	= true;
foreach ( $arrProfile as $objProfile ) {
	if ( $flgOK && $strProfile == "" || $strProfile == $objProfile->getName() ) {
		if ( $flgDebug ) {
			$objProfile->setDebug( true );
		}
		$flgOK	= $objProfile->drive();
	}
}

if ( $flgOK ) {
	echo "done.\n";
	exit;

} else {
	die( "Unknown error\n" );
}
?>