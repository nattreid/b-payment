<?php

declare(strict_types=1);

namespace NAttreid\BPayment\Hooks;

use Nette\SmartObject;

/**
 * Class BPaymentConfig
 *
 * @property string $secretKey
 * @property int $merchantNumber
 * @property int $gatewayId
 *
 * @author Attreid <attreid@gmail.com>
 */
class BPaymentConfig
{
	use SmartObject;

	/** @var string */
	private $secretKey;

	/** @var int */
	private $merchantNumber;

	/** @var int */
	private $gatewayId;

	protected function getSecretKey(): ?string
	{
		return $this->secretKey;
	}

	protected function setSecretKey(?string $secretKey)
	{
		$this->secretKey = $secretKey;
	}

	protected function getMerchantNumber(): ?int
	{
		return $this->merchantNumber;
	}

	protected function setMerchantNumber(?int $merchantNumber)
	{
		$this->merchantNumber = $merchantNumber;
	}

	protected function getGatewayId(): ?int
	{
		return $this->gatewayId;
	}

	protected function setGatewayId(?int $gatewayId)
	{
		$this->gatewayId = $gatewayId;
	}
}