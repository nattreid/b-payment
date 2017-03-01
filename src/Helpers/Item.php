<?php

declare(strict_types = 1);

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

	public function __construct(string $name, int $count, float $price)
	{
		$this->name = (string)$name;
		$this->count = intval($count);
		$this->price = floatval($price);
	}

	/**
	 * @return string
	 */
	protected function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	protected function getCount(): int
	{
		return $this->count;
	}

	/**
	 * @return float
	 */
	protected function getPrice(): float
	{
		return $this->price;
	}

	/**
	 * @return float
	 */
	protected function getTotalPrice(): float
	{
		return $this->price * $this->count;
	}
}