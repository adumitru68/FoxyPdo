### ConfigSample class

```php
namespace Name\Space\To;

use Qpdb\PdoWrapper\Exceptions\PdoWrapperException;
use Qpdb\PdoWrapper\Helpers\QueryTimer;
use Qpdb\PdoWrapper\Interfaces\PdoWrapperConfigInterface;

class ConfigSample implements PdoWrapperConfigInterface
{


	/**
	 * @return string
	 */
	public function getHost()
	{
		return 'localhost';
	}

	/**
	 * @return string
	 */
	public function getUser()
	{
		return 'your_mysql_user';
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return 'your_mysql_password';
	}

	/**
	 * @return string
	 */
	public function getDbName()
	{
		return 'db_name';
	}

	/**
	 * @param \Exception $e
	 * @param array $otherInformation
	 */
	public function handlePdoException( \Exception $e, $otherInformation = [] )
	{   
	    //log error message
	    throw new \Exception( $e->getMessage() );
	}


	/**
	 * @param string     $query
	 * @param QueryTimer $timer
	 * @return void
	 */
	public function handlePdoExecute( $query, QueryTimer $timer ) {
		// TODO: Implement handlePdoExecute() method.
	}
}
```