# B-Payments pro Nette Framework
Nastavení v **config.neon**
```neon
extensions:
    bPayments: NAttreid\BPayment\DI\BPaymentExtension

bPayments:
    secretKey: 'xxx1234x4xx54x65x456x4x88x9x987x'
    url: 'https://securepay.borgun.is/securepay/default.aspx'
    merchantNumber: 1234567
    gatewayId: 12345
```

### Použití
```php
/** @var \NAttreid\BPayment\IBPaymentClientFactory @inject */
public $bPaymentFactory;

protected function createComponentBPayment() {
    $bPayment = $this->$bPaymentFactory->create();
    
    $bPayment->setCancelUrl('//someUrl');
    $bPayment->setErrorUrl('//someUrl');
    $bPayment->setSuccessUrl('//someUrl');
    $bPayment->setOrderId(123456);
    $bPayment->setCurrency('CZK');
    $bPayment->setLanguage('CZ');
    $bPayment->addItem('Polozka', 4, 999.9);

    // uprava tlacitka
    $bPayment->button->value = 'Odeslat';
    $bPayment->button->setAttribute('class', 'button');
    
    return $bPayment;
}
```

```latte
{control $bPayment}
```