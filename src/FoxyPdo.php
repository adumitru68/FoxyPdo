<?php
/**
 * Created by PhpStorm.
 * User: Adi
 * Date: 8/11/2018
 * Time: 6:29 PM
 */

namespace Qpdb\PdoWrapper;


use Qpdb\PdoWrapper\Connection\FoxyPdoConnection;
use Qpdb\PdoWrapper\Helpers\FoxyPdoHelper;
use Qpdb\PdoWrapper\Interfaces\FoxyPdoConfigInterface;

class FoxyPdo
{

	/**
	 * @var FoxyPdoConfigInterface
	 */
	private $foxyPdoConfig;

	/**
	 * @var FoxyPdoConnection
	 */
	private $connection;

	/**
	 * @var FoxyPdoHelper
	 */
	private $foxyPdoHelper;



	public function __construct( FoxyPdoConfigInterface $foxyPdoConfig = null )
	{
		$this->foxyPdoHelper = new FoxyPdoHelper();

		if ( !is_null( $foxyPdoConfig ) ) {
			$this->foxyPdoConfig = $foxyPdoConfig;
			$this->connection = new FoxyPdoConnection( $foxyPdoConfig );
		}
	}


	/**
	 * @param FoxyPdoConfigInterface $foxyPdoConfig
	 * @return FoxyPdo
	 */
	public function setFoxyPdoConfig( $foxyPdoConfig )
	{
		$this->foxyPdoConfig = $foxyPdoConfig;
		$this->connection = new FoxyPdoConnection( $foxyPdoConfig );

		return $this;
	}


	/**
	 * @param string $query
	 * @param array $parameters
	 * @return \PDOStatement
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
			$this->foxyPdoConfig->handlePdoException( $e, [ 'query' => 'transaction' ] );

		}

		return $result;
	}

	public function transactionStart()
	{
		$this->connection->getPdo()->beginTransaction();
	}

	public function transactionEnd()
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


	private function queryInit( $query, array $params )
	{
		$startQueryTime = microtime( true );
		$queryStatement = null;
		$processedParameters = $this->foxyPdoHelper->prepareParams( $params );

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
				$this->foxyPdoConfig->handlePdoExecution( $query, microtime( true ) - $startQueryTime );
			}
			$queryStatement->execute();

		} catch ( \PDOException $e ) {
			$this->foxyPdoConfig->handlePdoException(
				$e,
				[
					'query' => $query,
					'parameters' => $params,
					'processedParameters' => $processedParameters
				]
			);
		}

		return $queryStatement;
	}


}