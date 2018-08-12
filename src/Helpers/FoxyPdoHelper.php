<?php
/**
 * Created by PhpStorm.
 * User: Adi
 * Date: 8/11/2018
 * Time: 7:40 PM
 */

namespace Qpdb\PdoWrapper\Helpers;


class FoxyPdoHelper
{

	/**
	 * @param array $parameters
	 * @return array
	 */
	public function prepareParams( array $parameters = [] )
	{
		$processedParameters = [];

		if ( $this->isArrayAssoc( $parameters ) )
			$processedParameters = $this->bindMore( $parameters );
		else
			foreach ( $parameters as $key => $val )
				$processedParameters[] = array( $key + 1, $val );

		return $processedParameters;
	}


	/**
	 * @param array $parameters
	 * @return array
	 */
	private function bindMore( array $parameters )
	{
		$processedParameters = [];
		$columns = array_keys( $parameters );
		foreach ( $columns as $i => &$column ) {
			$processedParameters[] = [':' . $column, $parameters[$column]];
		}

		return $processedParameters;
	}


	/**
	 * @param array $arr
	 * @return bool
	 */
	private function isArrayAssoc( array $arr )
	{
		if ( array() === $arr )
			return false;

		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}


}