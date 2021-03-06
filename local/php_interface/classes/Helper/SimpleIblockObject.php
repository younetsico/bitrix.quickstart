<?php

namespace Helper;

use Bitrix\Main\Application;

/**
 * Простой объект инфоблока, получающий только основные данные о записи (из таблицы b_iblock_element)
 * Нельзя получить свойства PROPERTY_* с помощью данного класса
 *
 * @example
 * $product = SimpleIblockObject::fromCode($code);
 * echo $product['NAME']; // $product->NAME;
 *
 *
 * @method static fromName($name) Если Имя уникально
 * @method static fromXmlId($xmlId) Если XML_ID уникально
 * @method static fromCode($code) Если CODE уникально
 *
 * Class SimpleIblockObject
 * @package Cetera
 */
class SimpleIblockObject implements \ArrayAccess
{

  private $params = array();

  private $allowedParamsFrom = array(
    'id' => 'ID',
    'name' => 'NAME',
    'xmlid' => 'XML_ID',
    'code' => 'CODE',
  );

  /**
   * @param mixed $value значение параметра по которому инициализируется объект
   * @param string $param параметр
   * @param int $iblockId инфоблок
   * @throws \Exception
   */
  public function __construct($value, $param = 'id', $iblockId = null)
  {
    $connection = Application::getConnection();
    $sqlHelper = $connection->getSqlHelper();
    $value = $sqlHelper->forSql($value);

    if($iblockId > 0)
      $iblockId = " AND IBLOCK_ID = " . intval($iblockId);

    if(!isset($this->allowedParamsFrom[$param]))
      throw new \Exception("Param {$param} not allowed");

    $param = $this->allowedParamsFrom[$param];

    $res = $connection->query("SELECT * FROM b_iblock_element WHERE `{$param}`='{$value}' {$iblockId} LIMIT 1")->fetch();

    if(!$res || !$res['ID'])
      return;

    $this->params = $res;
  }

  public static function fromId($id)
  {
    return new static($id, 'id');
  }

  /**
   * @param $name
   * @param $arguments
   * @return SimpleIblockObject
   * @throws \Exception
   */
  public static function __callStatic($name, $arguments)
  {
    if(strpos($name, 'from') !== 0)
      throw new \Exception("Method {$name} not allowed");

    return new static($arguments[0], toLower(substr($name, strlen('from'))), $arguments[1]);
  }

  function __get($name)
  {
    return $this->params[$name];
  }


  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   */
  public function offsetExists($offset)
  {
    return array_key_exists($offset, $this->params);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   * @return mixed Can return all value types.
   */
  public function offsetGet($offset)
  {
    return $this->params[$offset];
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   * @return void
   */
  public function offsetSet($offset, $value)
  {
    $this->params[$offset] = $value;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   * @return void
   */
  public function offsetUnset($offset)
  {
    unset($this->params[$offset]);
  }
}