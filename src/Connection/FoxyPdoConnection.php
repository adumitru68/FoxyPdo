<?php
/**
 * Created by PhpStorm.
 * User: Adi
 * Date: 8/11/2018
 * Time: 6:57 PM
 */

namespace Qpdb\PdoWrapper\Connection;


use Qpdb\PdoWrapper\Interfaces\FoxyPdoConfigInterface;

/**
 * Class FoxyPdoConnection
 * @package Qpdb\PdoWrapper\Connection
 */
final class FoxyPdoConnection
{

	/**
	 * @var FoxyPdoConfigInterface
	 */
	private $foxyPdoConfig;

	/**
	 * @var \PDO
	 */
	private $pdo;


	public function __construct( FoxyPdoConfigInterface $foxyPdoConfig )
	{
		$this->foxyPdoConfig = $foxyPdoConfig;
	}

	/**
	 * @return \PDO
	 */
	public function getPdo()
	{
		if ( !$this->pdo instanceof \PDO ) {
			$this->pdo = $this->connect();
		}

		return $this->pdo;
	}

	public function closeConnection()
	{
		$this->pdo = null;
	}

	/**
	 * @return \PDO
	 */
	private function connect()
	{
		$dsn = 'mysql:dbname=' . $this->foxyPdoConfig->getDbName() . ';host=' . $this->foxyPdoConfig->getHost() . '';
		$pdo = null;

		try {

			$pdo = new \PDO(
				$dsn,
				$this->foxyPdoConfig->getUser(),
				$this->foxyPdoConfig->getPassword(),
				[
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
				]
			);

			$pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			$pdo->setAttribute( \PDO::ATTR_EMULATE_PREPARES, false );

		} catch ( \PDOException $e ) {
			$this->foxyPdoConfig->handlePdoException( $e );
		}

		return $pdo;
	}


}