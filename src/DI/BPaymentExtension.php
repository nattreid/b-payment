<?php

declare(strict_types = 1);

namespace NAttreid\BPayment\DI;

use NAttreid\BPayment\BPaymentClient;
use NAttreid\BPayment\Hooks\BPaymentHook;
use NAttreid\BPayment\IBPaymentClientFactory;
use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\InvalidStateException;

/**
 * Class BPaymentExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
class BPaymentExtension extends CompilerExtension
{

	use ExtensionTranslatorTrait;

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

		$hook = $builder->getByType(HookService::class);
		if ($hook) {
			$builder->addDefinition($this->prefix('hook'))
				->setClass(BPaymentHook::class);

			$this->setTranslation(__DIR__ . '/../lang/', [
				'webManager'
			]);

			$config['secretKey'] = new Statement('?->bPaymentSecretKey', ['@' . Configurator::class]);
			$config['merchantNumber'] = new Statement('?->bPaymentMerchantNumber', ['@' . Configurator::class]);
			$config['gatewayId'] = new Statement('?->bPaymentGatewayId', ['@' . Configurator::class]);
		}

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