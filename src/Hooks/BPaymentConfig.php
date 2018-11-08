<?php

declare(strict_types=1);

namespace NAttreid\BPayment\Hooks;

use Nette\SmartObject;

/**
 * Class BPaymentConfig
 *
 * @property string|null $secretKey
 * @property int|null $merchantId
 * @property int|null $gatewayId
 *
 * @author Attreid <attreid@gmail.com>
 */
class BPaymentConfig
{
	use SmartObject;

	/** @var string|null */
	private $secretKey;

	/** @var int|null */
	private $merchantId;

	/** @var int|null */
	private $gatewayId;

	protected function getSecretKey(): ?string
	{
		return $this->secretKey;
	}

	protected function setSecretKey(?string $secretKey)
	{
		$this->secretKey = $secretKey;
	}

	protected function getMerchantId(): ?int
	{
		return $this->merchantId;
	}

	protected function setMerchantId(?int $merchantId)
	{
		$this->merchantId = $merchantId;
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