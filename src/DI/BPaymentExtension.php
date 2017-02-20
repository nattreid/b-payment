<?php

namespace NAttreid\BPayment\DI;

use NAttreid\BPayment\BPaymentClient;
use NAttreid\BPayment\IBPaymentClientFactory;
use Nette\DI\CompilerExtension;
use Nette\InvalidStateException;

/**
 * Class BPaymentExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
class BPaymentExtension extends CompilerExtension
{
	private $defaults = [
		'secretKey' => null,
		'url' => null,
		'merchantNumber' => null,
		'gatewayId' => null
	];

	public function loadConfiguration()
	{
		$config = $this->validateConfig($this->defaults, $this->getConfig());
		$builder = $this->getContainerBuilder();

		if ($config['secretKey'] === null) {
			throw new InvalidStateException("B-Payment: 'secretKey' does not set in config.neon");
		}
		if ($config['url'] === null) {
			throw new InvalidStateException("B-Payment: 'url' does not set in config.neon");
		}
		if ($config['merchantNumber'] === null) {
			throw new InvalidStateException("B-Payment: 'merchantNumber' does not set in config.neon");
		}
		if ($config['gatewayId'] === null) {
			throw new InvalidStateException("B-Payment: 'gatewayId' does not set in config.neon");
		}

		$builder->addDefinition($this->prefix('client'))
			->setImplement(IBPaymentClientFactory::class)
			->setFactory(BPaymentClient::class)
			->setArguments([$config['url'], $config['secretKey'], $config['merchantNumber'], $config['gatewayId']]);
	}
}