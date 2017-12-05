<?php

declare(strict_types=1);

namespace NAttreid\BPayment;

use NAttreid\BPayment\Helpers\Item;
use NAttreid\BPayment\Hooks\BPaymentConfig;
use NAttreid\Form\Form;
use Nette\Application\UI\Control;
use Nette\Application\UI\InvalidLinkException;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\InvalidStateException;

/**
 * Class BPaymentClient
 *
 * @author Attreid <attreid@gmail.com>
 */
class BPaymentClient extends Control
{

	/** @var string */
	private $url;

	/** @var BPaymentConfig */
	private $config;

	/** @var int */
	private $orderId;

	/** @var string */
	private $currency = 'EUR';

	/** @var string */
	private $language = 'EN';

	/** @var Item[] */
	private $items;

	private $redirectAfterPayment = true;

	/** @var float */
	private $amount;

	/** @var SessionSection */
	private $session;

	/** @var Request */
	private $request;

	/** @var string */
	private $button = __DIR__ . '/templates/button.latte';

	/** @var callback[] */
	public $onSuccess = [];

	/** @var callback[] */
	public $onCancel = [];

	/** @var callback[] */
	public $onError = [];

	public function __construct(string $url, BPaymentConfig $config, Session $session, Request $request)
	{
		parent::__construct();
		$this->url = $url;

		if ($config->secretKey === null) {
			throw new InvalidStateException("B-Payment: 'secretKey' does not set");
		}
		if ($config->merchantId === null) {
			throw new InvalidStateException("B-Payment: 'merchantID' does not set");
		}
		if ($config->gatewayId === null) {
			throw new InvalidStateException("B-Payment: 'gatewayId' does not set");
		}
		$this->config = $config;

		$this->session = $session->getSection('b-payment');
		$this->session->setExpiration('3 hours');

		$this->request = $request;
	}

	public function handleSuccess(): void
	{
		if ($this->verify()) {
			$this->onSuccess($this->request->getPost('orderid'), $this->request->getPost('authorizationcode'));
		} else {
			$this->handleError();
		}
	}

	public function handleError(): void
	{
		$this->onError($this->request->getPost('errorcode'), $this->request->getPost('errordescription'));
	}

	public function handleCancel(): void
	{
		$this->onCancel();
	}

	public function setRedirectAfterPayment(bool $redirect = false): void
	{
		$this->redirectAfterPayment = $redirect;
	}

	/**
	 * Nastavi sablonu tlacitka
	 * @param string $button
	 * @return static
	 */
	public function setButton(string $button): self
	{
		$this->button = $button;
		return $this;
	}

	/**
	 * @param int $orderId
	 * @return static
	 */
	public function setOrderId(int $orderId): self
	{
		$this->orderId = $orderId;
		return $this;
	}

	/**
	 * Nastavi menu. Povolene hodnoty jsou GBP, USD, EUR, DKK, NOK, SEK, CHF, CAD, HUF, BHD, AUD, RUB, PLN, RON, HRK, CZK, ISK
	 * @param string $currency
	 * @return static
	 */
	public function setCurrency(string $currency): self
	{
		$this->currency = $currency;
		return $this;
	}

	/**
	 * Nastavi jazyk brany. Povolene hodnoty jsou CZ, IS, EN, DE, FR, RU, ES, IT, PT, SI, HU, SE, NL, PL, NO, SK, HR, SR, RO, DK, FI, FO
	 * @param string $language
	 * @return static
	 */
	public function setLanguage(string $language): self
	{
		$this->language = $language;
		return $this;
	}

	/**
	 * Prida polozku do brany
	 * @param string $name
	 * @param int $count
	 * @param float $price
	 * @return static
	 */
	public function addItem(string $name, int $count, float $price): self
	{
		$item = new Item($name, $count, $price);
		$this->items[] = $item;
		$this->amount += $item->totalPrice;
		return $this;
	}

	/**
	 * @return Form
	 * @throws InvalidLinkException
	 */
	protected function createComponentPaymentForm(): Form
	{
		$successLink = $this->link('//success');
		$cancelLink = $this->link('//cancel');
		$errorLink = $this->link('//error');

		$hash = $this->hash($this->config->merchantId, $successLink, $successLink, $this->orderId, $this->amount, $this->currency);

		$this->session->orderHash = $this->hash($this->orderId, $this->amount, $this->currency);

		$form = new Form;
		$form->setAction($this->url);

		$form->addHidden('merchantid', $this->config->merchantId);
		$form->addHidden('paymentgatewayid', $this->config->gatewayId);
		$form->addHidden('skipreceiptpage', $this->redirectAfterPayment ? 1 : 0);
		$form->addHidden('orderid', $this->orderId);
		$form->addHidden('reference', $this->orderId);
		$form->addHidden('checkhash', $hash);
		$form->addHidden('currency', $this->currency);
		$form->addHidden('language', $this->language);
		$form->addHidden('returnurlsuccess', $successLink);
		$form->addHidden('returnurlcancel', $cancelLink);
		$form->addHidden('returnurlerror', $errorLink);

		foreach ($this->items as $key => $item) {
			$form->addHidden('itemdescription_' . $key, $item->name);
			$form->addHidden('itemcount_' . $key, $item->count);
			$form->addHidden('itemunitamount_' . $key, $item->price);
			$form->addHidden('itemamount_' . $key, $item->totalPrice);
		}
		$form->addHidden('amount', $this->amount);

		return $form;
	}

	/**
	 * Verifikuje platbu
	 * @return bool
	 */
	private function verify(): bool
	{
		$orderhash = $this->request->getPost('orderhash');
		if ($orderhash === $this->session->orderHash) {
			return true;
		}
		return false;
	}

	/**
	 * @param mixed[] ...$data
	 * @return string
	 */
	private function hash(...$data): string
	{
		return hash_hmac('sha256', implode('|', $data), $this->config->secretKey);
	}

	public function render(): void
	{
		$this->template->setFile(__DIR__ . '/templates/bpayment.latte');
		$this->template->button = $this->button;
		$this->template->render();
	}
}

interface IBPaymentClientFactory
{
	public function create(): BPaymentClient;
}
