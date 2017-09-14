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

		$hook = $builder->getByType(HookService::class);
		if ($hook) {
			$builder->addDefinition($this->prefix('hook'))
				->setType(BPaymentHook::class);

			$this->setTranslation(__DIR__ . '/../lang/', [
				'webManager'
			]);

			$bPayment = new Statement('?->bPayment \?: new ' . BPaymentConfig::class, ['@' . Configurator::class]);
		} else {
			$bPayment = new BPaymentConfig();
			$bPayment->secretKey = $config['secretKey'];
			$bPayment->merchantId = $config['merchantId'];
			$bPayment->gatewayId = $config['gatewayId'];
		}

		$builder->addDefinition($this->prefix('client'))
			->setImplement(IBPaymentClientFactory::class)
			->setFactory(BPaymentClient::class)
			->setArguments([$config['url'], $bPayment]);
	}
}