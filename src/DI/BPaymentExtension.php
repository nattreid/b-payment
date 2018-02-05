<?php
declare(strict_types=1);

namespace NAttreid\BPayment\DI;

use NAttreid\BPayment\Hooks\BPaymentConfig;
use NAttreid\BPayment\Hooks\BPaymentHook;
use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\Statement;

if (trait_exists('NAttreid\Cms\DI\ExtensionTranslatorTrait')) {
	class BPaymentExtension extends AbstractBPaymentExtension
	{
		use ExtensionTranslatorTrait;

		protected function prepareHook(array $bPayment): BPaymentConfig
		{
			$builder = $this->getContainerBuilder();
			$hook = $builder->getByType(HookService::class);
			if ($hook) {
				$builder->addDefinition($this->prefix('hook'))
					->setType(BPaymentHook::class);

				$this->setTranslation(__DIR__ . '/../lang/', [
					'webManager'
				]);

				$bPayment = new Statement('?->bPayment \?: new ' . BPaymentConfig::class, ['@' . Configurator::class]);
			} else {
				$bPayment = parent::prepareHook($bPayment);
			}
			return $bPayment;
		}
	}
} else {
	class BPaymentExtension extends AbstractBPaymentExtension
	{
	}
}