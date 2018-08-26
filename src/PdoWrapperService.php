<?php
/**
 * Created by PhpStorm.
 * User: Adi
 * Date: 8/11/2018
 * Time: 6:29 PM
 */

namespace Qpdb\PdoWrapper;


use Qpdb\PdoWrapper\Connection\PdoWrapperConnection;
use Qpdb\PdoWrapper\Helpers\PdoWrapperHelper;
use Qpdb\PdoWrapper\Interfaces\PdoWrapperConfigInterface;

class PdoWrapperService
{

	private static $instance;

	/**
	 * @var PdoWrapperConfigInterface
	 */
	private $pdoConfig;

	/**
	 * @var PdoWrapperConnection
	 */
	private $connection;

	/**
	 * @var PdoWrapperHelper
	 */
	private $pdoWrapperHelper;


	/**
	 * PdoWrapperService constructor.
	 * @param PdoWrapperConfigInterface|null $foxyPdoConfig
	 */
	public function __construct( PdoWrapperConfigInterface $foxyPdoConfig = null )
	{
		$this->pdoWrapperHelper = new PdoWrapperHelper();

		if ( !is_null( $foxyPdoConfig ) ) {
			$this->setPdoWrapperConfig( $foxyPdoConfig );
		}
	}


	/**
	 * @param PdoWrapperConfigInterface $foxyPdoConfig
	 * @return PdoWrapperService
	 */
	public function setPdoWrapperConfig( PdoWrapperConfigInterface $foxyPdoConfig )
	{
		$this->pdoConfig = $foxyPdoConfig;
		$this->connection = new PdoWrapperConnection( $foxyPdoConfig );

		return $this;
	}


	/**
	 * @param $query
	 * @param array $parameters
	 * @return bool|\PDOStatement
	 */
	public function query( $query, $parameters = [] )
	{
		return $this->queryInit( $query, $parameters );
	}

	/**
	 * @param $query
	 * @param array $parameters
	 * @param int $fetchMode
	 * @return array
	 */
	public function queryFetch( $query, $parameters = [], $fetchMode = \PDO::FETCH_ASSOC )
	{
		return $this->query( $query, $parameters )->fetch( $fetchMode );
	}


	/**
	 * @param $query
	 * @param array $parameters
	 * @param int $fetchMode
	 * @return array
	 */
	public function queryFetchAll( $query, $parameters = [], $fetchMode = \PDO::FETCH_ASSOC )
	{
		return $this->query( $query, $parameters )->fetchAll( $fetchMode );
	}


	/**
	 * @param \Closure $closure
	 * @param array $params
	 * @return mixed|null
	 */
	public function transaction( \Closure $closure, array $params = [] )
	{
		$result = null;

		try {

			$this->connection->getPdo()->beginTransaction();
			$result = call_user_func_array( $closure, $params );
			$this->connection->getPdo()->commit();

		} catch ( \PDOException $e ) {

			$this->connection->getPdo()->rollBack();
			$result = null;
			$this->pdoConfig->handlePdoException( $e, [ 'query' => 'transaction' ] );

		}

		return $result;
	}

	public function transactionStart()
	{
		$this->connection->getPdo()->beginTransaction();
	}

	public function transactionCommit()
	{
		$this->connection->getPdo()->commit();
	}

	public function transactionRollBack()
	{
		$this->connection->getPdo()->rollBack();
	}

	/**
	 * @return string
	 */
	public function lastInsertId()
	{
		return $this->connection->getPdo()->lastInsertId();
	}

	/**
	 * @param $query
	 * @param array $params
	 * @return bool|\PDOStatement
	 */
	private function queryInit( $query, array $params )
	{
		$startQueryTime = microtime( true );
		$processedParameters = $this->pdoWrapperHelper->prepareParams( $params );

		try {

			$queryStatement = $this->connection->getPdo()->prepare( $query );
			foreach ( $processedParameters as $param => $value ) {
				if ( is_int( $value[ 1 ] ) ) {
					$type = \PDO::PARAM_INT;
				}
				elseif ( is_bool( $value[ 1 ] ) ) {
					$type = \PDO::PARAM_BOOL;
				}
				elseif ( is_null( $value[ 1 ] ) ) {
					$type = \PDO::PARAM_NULL;
				}
				else {
					$type = \PDO::PARAM_STR;
				}
				$queryStatement->bindValue( $value[ 0 ], $value[ 1 ], $type );
				$this->pdoConfig->handlePdoExecute( $query, microtime( true ) - $startQueryTime );
			}
			$queryStatement->execute();

		} catch ( \PDOException $e ) {
			$this->pdoConfig->handlePdoException(
				$e,
				[
					'query' => $query,
					'parameters' => $params,
					'processedParameters' => $processedParameters
				]
			);
			return false;
		}

		return $queryStatement;
	}

	/**
	 * @return $this
	 */
	public static function getInstance()
	{
		if ( !self::$instance )
			self::$instance = new static();

		return self::$instance;
	}

}