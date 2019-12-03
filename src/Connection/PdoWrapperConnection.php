<?php
/**
 * Created by PhpStorm.
 * User: Adi
 * Date: 8/11/2018
 * Time: 6:57 PM
 */

namespace Qpdb\PdoWrapper\Connection;


use Qpdb\PdoWrapper\Interfaces\PdoWrapperConfigInterface;

/**
 * Class FoxyPdoConnection
 * @package Qpdb\PdoWrapper\Connection
 */
final class PdoWrapperConnection
{

	/**
	 * @var PdoWrapperConfigInterface
	 */
	private $pdoConfig;

	/**
	 * @var array
	 */
	private $pdoConfigOptionsDefault = [
		\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_EMULATE_PREPARES => false,
	];

	/**
	 * @var array
	 */
	private $execCommands;


	/**
	 * @var \PDO
	 */
	private $pdo;


	/**
	 * PdoWrapperConnection constructor.
	 * @param PdoWrapperConfigInterface $pdoConfig
	 */
	public function __construct( PdoWrapperConfigInterface $pdoConfig ) {
		$this->pdoConfig = $pdoConfig;
		$this->execCommands = $this->pdoConfig->getExecCommands();
	}

	/**
	 * @return \PDO
	 */
	public function getPdo() {
		if ( !$this->pdo instanceof \PDO ) {
			$this->pdo = $this->connect();
		}

		return $this->pdo;
	}

	public function closeConnection() {
		$this->pdo = null;
	}

	/**
	 * @return \PDO
	 */
	private function connect() {
		$dsn = 'mysql:dbname=' . $this->pdoConfig->getDbName() . ';host=' . $this->pdoConfig->getHost() . '';
		$pdo = null;

		$options = $this->pdoConfigOptionsDefault;

		foreach ($this->pdoConfig->getOptions() as $key => $val) {
			$options[$key] = $val;
		}

		try {

			$pdo = new \PDO(
				$dsn,
				$this->pdoConfig->getUser(),
				$this->pdoConfig->getPassword(),
				$options
			);

			foreach ( $this->execCommands as $command ) {
				$pdo->exec( $command );
			}

		} catch ( \PDOException $e ) {
			$this->pdoConfig->handlePdoException( $e );
		}

		return $pdo;

	}


}