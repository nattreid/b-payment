<?php

declare(strict_types=1);

namespace NAttreid\BPayment\DI;

use NAttreid\BPayment\BPaymentClient;
use NAttreid\BPayment\Hooks\BPaymentConfig;
use NAttreid\BPayment\IBPaymentClientFactory;
use Nette\DI\CompilerExtension;
use Nette\InvalidStateException;

/**
 * Class AbstractBPaymentExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class AbstractBPaymentExtension extends CompilerExtension
{

	private $defaults = [
		'secretKey' => null,
		'url' => null,
		'merchantId' => null,
		'gatewayId' => null
	];

	public function loadConfiguration(): void
	{
		$config = $this->validateConfig($this->defaults, $this->getConfig());
		$builder = $this->getContainerBuilder();

		if ($config['url'] === null) {
			throw new InvalidStateException("B-Payment: 'url' does not set in config.neon");
		}

		$bPayment = $this->prepareHook($config);

		$builder->addDefinition($this->prefix('client'))
			->setImplement(IBPaymentClientFactory::class)
			->setFactory(BPaymentClient::class)
			->setArguments([$config['url'], $bPayment]);
	}

	protected function prepareHook(array $config): BPaymentConfig
	{
		$bPayment = new BPaymentConfig();
		$bPayment->secretKey = $config['secretKey'];
		$bPayment->merchantId = $config['merchantId'];
		$bPayment->gatewayId = $config['gatewayId'];
	}
}