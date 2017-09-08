<?php

declare(strict_types=1);

namespace NAttreid\BPayment\Hooks;

use NAttreid\Form\Form;
use NAttreid\WebManager\Services\Hooks\HookFactory;
use Nette\ComponentModel\Component;
use Nette\Utils\ArrayHash;

/**
 * Class BPaymentHook
 *
 * @author Attreid <attreid@gmail.com>
 */
class BPaymentHook extends HookFactory
{

	/** @var IConfigurator */
	protected $configurator;

	public function init(): void
	{
		if (!$this->configurator->bPayment) {
			$this->configurator->bPayment = new BPaymentConfig;
		}
	}

	/** @return Component */
	public function create(): Component
	{
		$form = $this->formFactory->create();
		$form->setAjaxRequest();

		$form->addText('secretKey', 'webManager.web.hooks.bPayment.secretKey')
			->setDefaultValue($this->configurator->bPayment->secretKey);
		$form->addInteger('merchantId', 'webManager.web.hooks.bPayment.merchantId')
			->setDefaultValue($this->configurator->bPayment->merchantId);
		$form->addInteger('gatewayId', 'webManager.web.hooks.bPayment.gatewayId')
			->setDefaultValue($this->configurator->bPayment->gatewayId);

		$form->addSubmit('save', 'form.save');

		$form->onSuccess[] = [$this, 'bPaymentFormSucceeded'];

		return $form;
	}

	public function bPaymentFormSucceeded(Form $form, ArrayHash $values): void
	{
		$config = $this->configurator->bPayment;

		$config->secretKey = $values->secretKey ?: null;
		$config->merchantId = $values->merchantId ?: null;
		$config->gatewayId = $values->gatewayId ?: null;

		$this->configurator->bPayment = $config;

		$this->flashNotifier->success('default.dataSaved');
	}
}