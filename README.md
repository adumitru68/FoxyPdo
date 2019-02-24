# pdoWrapper
Mysql pdo wrapper library.

### Requirements
* Php 5.6+
* Enable PDO (php.ini)
* MySql / MariaDB

### Installation

```
composer require qpdb/pdo-wrapper
```

### Configuration

Create a configuration class that ```ConfigSample``` implements the ```Qpdb\PdoWrapper\Interfaces\PdoWrapperConfigInterface```  interface as in the following example:

[ConfigSample class](docs/ConfigSample.md).

### How to use

```php
require_once __DIR__ . '/../vendor/autoload.php';

use Name\Space\To\ConfigSample;
use Qpdb\PdoWrapper\PdoWrapperService;
$configSample = new ConfigSample();
PdoWrapperService::getInstance()->setPdoWrapperConfig($configDb);

$sql = "SELECT `id`, `name`, `icon` FROM `categories` WHERE `id` < ?";
$result = PdoWrapperService::getInstance()->queryFetchAll($sql, [3]);

/** or */

$sql = "SELECT `id`, `name`, `icon` FROM `categories` WHERE `id` < :id";
$result = PdoWrapperService::getInstance()->queryFetchAll($sql, ['id'=>3]);

/** returns */
```

```queryFetchAll(...) //return rows array```
```php
Array
(
    [0] => Array
        (
            [id] => 1
            [name] => Category 1
            [icon] => d110c4a10a43d77f966ef5ea332df376.png
        )

    [1] => Array
        (
            [id] => 2
            [name] => Category 2
            [icon] => f1dc8b7842701001171f8c3e4e229ccc.jpg
        )

)
```

```queryFetch(...) return one row```
```php
Array
(
    [id] => 1
    [name] => Category 1
    [icon] => d110c4a10a43d77f966ef5ea332df376.png
)
```

```query(...) return PdoStatement```








