<?php

declare(strict_types=1);

namespace NAttreid\BPayment;

use NAttreid\BPayment\Helpers\Item;
use NAttreid\Form\Form;
use Nette\Application\UI\Control;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\SessionSection;

/**
 * Class BPaymentClient
 *
 * @author Attreid <attreid@gmail.com>
 */
class BPaymentClient extends Control
{

	/** @var string */
	private $url;

	/** @var string */
	private $secretKey;

	/** @var int */
	private $merchantNumber;

	/** @var int */
	private $gatewayId;

	/** @var int */
	private $orderId;

	/** @var string */
	private $currency = 'EUR';

	/** @var string */
	private $language = 'EN';

	/** @var Item[] */
	private $items;

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

	public function __construct(string $url, string $secretKey, int $merchantNumber, int $gatewayId, Session $session, Request $request)
	{
		parent::__construct();
		$this->url = $url;
		$this->secretKey = $secretKey;
		$this->merchantNumber = $merchantNumber;
		$this->gatewayId = $gatewayId;

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

	/**
	 * Nastavi sablonu tlacitka
	 * @param string $button
	 */
	public function setButton(string $button): void
	{
		$this->button = $button;
	}

	/**
	 * @param int $orderId
	 */
	public function setOrderId(int $orderId): void
	{
		$this->orderId = $orderId;
	}

	/**
	 * Nastavi menu. Povolene hodnoty jsou GBP, USD, EUR, DKK, NOK, SEK, CHF, CAD, HUF, BHD, AUD, RUB, PLN, RON, HRK, CZK, ISK
	 * @param string $currency
	 */
	public function setCurrency(string $currency): void
	{
		$this->currency = $currency;
	}

	/**
	 * Nastavi jazyk brany. Povolene hodnoty jsou CZ, IS, EN, DE, FR, RU, ES, IT, PT, SI, HU, SE, NL, PL, NO, SK, HR, SR, RO, DK, FI, FO
	 * @param string $language
	 */
	public function setLanguage(string $language): void
	{
		$this->language = $language;
	}

	/**
	 * Prida polozku do brany
	 * @param string $name
	 * @param int $count
	 * @param float $price
	 */
	public function addItem(string $name, int $count, float $price): void
	{
		$item = new Item($name, $count, $price);
		$this->items[] = $item;
		$this->amount += $item->totalPrice;
	}

	protected function createComponentPaymentForm(): Form
	{
		$successLink = $this->link('//success');
		$cancelLink = $this->link('//cancel');
		$errorLink = $this->link('//error');

		$hash = $this->hash($this->merchantNumber, $successLink, $successLink, $this->orderId, $this->amount, $this->currency);

		$this->session->orderHash = $this->hash($this->orderId, $this->amount, $this->currency);

		$form = new Form;
		$form->setAction($this->url);

		$form->addHidden('merchantid', $this->merchantNumber);
		$form->addHidden('paymentgatewayid', $this->gatewayId);
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
		return hash_hmac('sha256', implode('|', $data), $this->secretKey);
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
