<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Cake\Database\Driver;

use Cake\Database\Dialect\SqliteDialectTrait;
use Cake\Database\Query;
use Cake\Database\Statement\PDOStatement;
use Cake\Database\Statement\SqliteStatement;
use PDO;

class Sqlite extends \Cake\Database\Driver {

	use PDODriverTrait;
	use SqliteDialectTrait;

/**
 * Base configuration settings for Sqlite driver
 *
 * @var array
 */
	protected $_baseConfig = [
		'persistent' => false,
		'login' => null,
		'password' => null,
		'database' => ':memory:',
		'encoding' => 'utf8',
		'flags' => [],
		'init' => [],
		'dsn' => null
	];

/**
 * Establishes a connection to the databse server
 *
 * @return bool true on success
 */
	public function connect() {
		if ($this->_connection) {
			return true;
		}
		$config = $this->_config;
		$config['flags'] += [
			PDO::ATTR_PERSISTENT => $config['persistent'],
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		];

		if (empty($config['dsn'])) {
			$config['dsn'] = "sqlite:{$config['database']}";
		}

		$this->_connect($config);

		if (!empty($config['init'])) {
			foreach ((array)$config['init'] as $command) {
				$this->connection()->exec($command);
			}
		}
		return true;
	}

/**
 * Returns whether php is able to use this driver for connecting to database
 *
 * @return bool true if it is valid to use this driver
 */

	public function enabled() {
		return in_array('sqlite', PDO::getAvailableDrivers());
	}

/**
 * Prepares a sql statement to be executed
 *
 * @param string|\Cake\Database\Query $query
 * @return \Cake\Database\StatementInterface
 */
	public function prepare($query) {
		$this->connect();
		$statement = $this->_connection->prepare((string)$query);
		return new SqliteStatement(new PDOStatement($statement, $this), $this);
	}

}
