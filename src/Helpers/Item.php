<?php

namespace NAttreid\BPayment\Helpers;

use Nette\SmartObject;

/**
 * Class Item
 *
 * @property-read string $name
 * @property-read int $count
 * @property-read float $price
 * @property-read float $totalPrice
 *
 * @author Attreid <attreid@gmail.com>
 */
class Item
{

	use SmartObject;

	/** @var string */
	private $name;

	/** @var int */
	private $count;

	/** @var float */
	private $price;

	/**
	 * Item constructor.
	 * @param string $name
	 * @param int $count
	 * @param float $price
	 */
	public function __construct($name, $count, $price)
	{
		$this->name = (string)$name;
		$this->count = intval($count);
		$this->price = floatval($price);
	}

	/**
	 * @return string
	 */
	protected function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	protected function getCount()
	{
		return $this->count;
	}

	/**
	 * @return float
	 */
	protected function getPrice()
	{
		return $this->price;
	}

	/**
	 * @return float
	 */
	protected function getTotalPrice()
	{
		return $this->price * $this->count;
	}
}