<?php
class QSBackUpperDriverMySQL extends QSBackUpperDriverBase {
	private	$strDBHost		= "localhost";
	private	$intDBPort		= 3306;
	private	$strDBUser		= "";
	private	$strDBPass		= "";
	private	$strDBName		= "";
	private	$intDepth			= 7;
	private	$flgMonthly		= false;

	public function setDBHost( $objVal ) {
		$this->strDBHost		= $objVal;
		return $this;
	}

	public function setDBPort( $objVal ) {
		$this->intDBPort		= $objVal;
		return $this;
	}

	public function setDBUser( $objVal ) {
		$this->strDBUser		= $objVal;
		return $this;
	}

	public function setDBPass( $objVal ) {
		$this->strDBPass		= $objVal;
		return $this;
	}

	public function setDBName( $objVal ) {
		$this->strDBName		= $objVal;
		return $this;
	}

	public function setDepth( $objVal ) {
		$this->intDepth			= $objVal;
		return $this;
	}

	public function setMonthly( $objVal ) {
		$this->flgMonthly		= $objVal;
		return $this;
	}


	public function drive() {
		// 汎用判定
		if ( !parent::drive() ) {
			return false;
		}

		// DBホスト未指定
		if ( $this->strDBHost == "" ) {
			echo "QSBackUpperDriverMySQL::strDBHost : Undefined.\n";
			return false;

		// DBユーザ未指定
		} elseif ( $this->strDBUser == "" ) {
			echo "QSBackUpperDriverMySQL::strDBUser : Undefined.\n";
			return false;

		// DBパスワード未指定
		} elseif ( $this->strDBPass == "" ) {
			echo "QSBackUpperDriverMySQL::strDBPass : Undefined.\n";
			return false;

		// DB名未指定
		} elseif ( $this->strDBName == "" ) {
			echo "QSBackUpperDriverMySQL::strDBName : Undefined.\n";
			return false;

		}
		$strFileSave		= date( "Ymd" ) . ".zip";
		$strFileDelete	= ( $this->intDepth > 0 )? date( "Ymd", mktime( 0,0,0, date( "m" ), date( "d" ) - $this->intDepth, date( "Y" ) ) ) . ".zip": "";
		$strFileKeep		= ( $this->flgMonthly )? date( "Ymt" ) . ".zip": "";
		$strCmd			= "ssh -p {$this->getSSHPort()} -i {$this->getSSHKey(true)} {$this->getSSHUser()}@{$this->getSSHHost()} \"mysqldump -u{$this->strDBUser} -p'{$this->strDBPass}' -h{$this->strDBHost} -P{$this->intDBPort} {$this->strDBName} | gzip -c\" > {$this->getDst()}/{$strFileSave}" ;

		if (
			$this->cmd( $strCmd ) &&
			file_exists( "{$this->getDst()}/{$strFileSave}" ) &&
			filesize( "{$this->getDst()}/{$strFileSave}" ) > 0
		) {
			if ( $this->intDepth > 0 && file_exists( "{$this->getDst()}/{$strFileDelete}" ) && $strFileDelete != $strFileKeep ) {
				cmd( "rm -Rf {$this->getDst()}/{$strFileDelete}" );
			}
			return true;
		}

		return false;
	}
}
?>