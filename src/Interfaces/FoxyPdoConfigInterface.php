<?php
/**
 * Created by PhpStorm.
 * User: Adi
 * Date: 8/11/2018
 * Time: 6:46 PM
 */

namespace Qpdb\PdoWrapper\Interfaces;


interface FoxyPdoConfigInterface
{

	/**
	 * @return string
	 */
	public function getHost();

	/**
	 * @return string
	 */
	public function getUser();

	/**
	 * @return string
	 */
	public function getPassword();

	/**
	 * @return string
	 */
	public function getDbName();

	/**
	 * @param \PDOException $e
	 * @param array $otherInformation
	 */
	public function handlePdoException( \PDOException $e, $otherInformation = [] );

	/**
	 * @param string $query
	 * @param double $duration
	 */
	public function handlePdoExecute( $query, $duration );


}