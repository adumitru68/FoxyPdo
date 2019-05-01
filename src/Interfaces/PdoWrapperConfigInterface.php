<?php
/**
 * Created by PhpStorm.
 * User: Adi
 * Date: 8/11/2018
 * Time: 6:46 PM
 */

namespace Qpdb\PdoWrapper\Interfaces;


use Qpdb\PdoWrapper\Helpers\QueryTimer;

interface PdoWrapperConfigInterface
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
	 * @return array
	 */
	public function getPdoOptions();

	/**
	 * @return array
	 */
	public function getExecCommands();


	/**
	 * @param \Exception $e
	 * @param array $otherInformation
	 */
	public function handlePdoException( \Exception $e, $otherInformation = [] );

	/**
	 * @param string     $query
	 * @param QueryTimer $timer
	 * @return void
	 */
	public function handlePdoExecute( $query, QueryTimer $timer );


}