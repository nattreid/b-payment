<?php

declare(strict_types=1);

namespace NAttreid\BPayment\DI;

use NAttreid\BPayment\BPaymentClient;
use NAttreid\BPayment\Hooks\BPaymentConfig;
use NAttreid\BPayment\Hooks\BPaymentHook;
use NAttreid\BPayment\IBPaymentClientFactory;
use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;
use Nette\InvalidStateException;

/**
 * Class BPaymentExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
class AbstractBPaymentExtension extends CompilerExtension
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