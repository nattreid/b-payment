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

protected function createComponentBPayment($name) {
    $bPayment = $this->$bPaymentFactory->create($this, $name);
    
    $bPayment->setOrderId(123456);
    $bPayment->setCurrency('CZK');
    $bPayment->setLanguage('CZ');
    $bPayment->addItem('Polozka', 4, 999.9);

    $bPayment->setButton(__DIR__.'/button.latte'); // zmena sablony tlacitka
    
    $bPayment->onSuccess[] = function($orderId, $authorizationCode) {
        
    }
    $bPayment->onError[] = function($errorCode, $errorDescription) {
        
    }
    $bPayment->onCancel[] = function() {
        
    }
    
    return $bPayment;
}
```

```latte
{control $bPayment}
```