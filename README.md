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

[ConfigSample class]('docs/ConfigSample.md).

```php
use Name\Space\To\ConfigSample;
use Qpdb\PdoWrapper\PdoWrapperService;
$configSample = new ConfigSample();
PdoWrapperService::getInstance()->setPdoWrapperConfig($configDb);
```



