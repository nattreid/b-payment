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

	/** @return Component */
	public function create(): Component
	{
		$form = $this->formFactory->create();
		$form->setAjaxRequest();

		$form->addText('secretKey', 'webManager.web.hooks.bPayment.secretKey')
			->setDefaultValue($this->configurator->bPaymentSecretKey);
		$form->addText('merchantNumber', 'webManager.web.hooks.bPayment.merchantNumber')
			->setDefaultValue($this->configurator->bPaymentMerchantNumber);
		$form->addText('gatewayId', 'webManager.web.hooks.bPayment.gatewayId')
			->setDefaultValue($this->configurator->bPaymentGatewayId);

		$form->addSubmit('save', 'form.save');

		$form->onSuccess[] = [$this, 'bPaymentFormSucceeded'];

		return $form;
	}

	public function bPaymentFormSucceeded(Form $form, ArrayHash $values): void
	{
		$this->configurator->bPaymentSecretKey = $values->secretKey;
		$this->configurator->bPaymentMerchantNumber = $values->merchantNumber;
		$this->configurator->bPaymentGatewayId = $values->gatewayId;

		$this->flashNotifier->success('default.dataSaved');
	}
}