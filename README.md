**DEPRECATED** 

Lütfen dikkat, bu proje henüz Slim Framework son haline gelmeden önce şuanki mevcut bazı özellikleri de içeren ve yazılım geliştirmeyi kolaylaştıran bazı pratikleri içeriyordu. 
Slim Framework tarafındaki bazı gelişmeler, örneğin kendi Container'ından vazgeçip PHP-DI gibi diğer container'ları bir interface üzerinden kullanma gibi geliştirmeleri ve pratikleri
son versiyonunda içine aldığı için bu projeye artık gerek kalmamıştır. Slim Framework ile başlanan bir projeyi [slim-bridge](https://github.com/PHP-DI/Slim-Bridge)'i dahil ederek 
kullanmaya devam edebilirsiniz.

# Karma Framework

[![Build Status](https://travis-ci.org/karmaphp/karma.svg?branch=master)](https://travis-ci.org/karmaphp/karma)
[![Coverage Status](https://coveralls.io/repos/github/karmaphp/karma/badge.svg?branch=master)](https://coveralls.io/github/karmaphp/karma?branch=master)
[![Total Downloads](https://poser.pugx.org/karmaphp/karma/downloads)](https://packagist.org/packages/karmaphp/karma)

Mikro frameworklerden Slim Framework üzerine inşa edilmiş bir PHP Application Framework'tür. Slim Framework ile tamamen uyumludur. Bu sayfada anlatılanlara ek olarak onun dökümantasyonunu da kullanabilirsiniz.

## Misyon

PHP ile uygulama geliştirmeye alternatif bir çözüm önerisi sunmak. PHP'nin kaygan zemininden kaçarak Framework'lere sığınanlar için alternatif olmak.

## Vizyon

Ortaya koyulan standartlar ve önerileri herkesin benimseyerek kullanması ve önerilerle genelin ihtiyacını karşılayan bir yapı sunmak.

## Composer ile Yükle

```json
{
  "require": {
    "karmaphp/karma": "^2.2"
  }
}
```

## Booting (index.php)

```php
<?php

require_once 'vendor/autoload.php';

$app = \Karma\AppFactory::create();

$app->run();
```

## Container'ın Aktif Edilmesi

Karma Framework `php-di/php-di` paketi ile birlikte gelmektedir ve varsayılan container olarak **php-di** kullanmaktadır.

Container build ederken ilk parametre olarak Container sınıfı, ikinci olarak da servisler **array** olarak verilmelidir. Üçüncü parametre olarak $useAnnotations değişkeni varsayılan oloarak true olduğu için annotation injection varsayılan olarak desteklenmektedir ve kullanımı tavsiye edilmektedir.

Container servislerine `$container->get('smarty')` şeklinde ya da `$container->smarty` şeklinde ulaşabilirsiniz.

```php
<?php

require_once 'vendor/autoload.php';

$container = \Karma\ContainerBuilder::build(
    \Karma\Container::class,
    [
        'smarty' => \DI\get(\Karma\Service\SmartyService::class)
    ]
);

$app = \Karma\AppFactory::create($container);

$app->run();
```

## Routing

Routing stratejisi olarak `[\App\Controller\MainController::class, 'Index']` şeklinde bir kullanım tercih edilmiştir.

```php
<?php

require_once 'vendor/autoload.php';

$container = \Karma\ContainerBuilder::build(
    \Karma\Container::class,
    [
        'smarty' => \DI\get(\Karma\Service\SmartyService::class)
    ]
);

$app = \Karma\AppFactory::create($container);

$app->get('/', [\App\Controller\MainController::class, 'Index']);

$app->run();
```

## Controller

Karma Framework'de yazacağınız bir sınıfı Controller olarak kullanmak mümkün. Controller sınıfları için tavsiye edilen klasör `app/Controller`.

```php
<?php namespace App\Controller;

use \Karma\Controller;

class MainController extends Controller
{
    public function Index()
    {
        return 'Merhaba Dünya';
    }
}
```

Controller fonksiyonları `string` ya da `ResponseInterface` dönebilir.

**JSON** response için $this->json() **XML** response için ise $this->xml() fonksiyonları kullanılabilir.

## View
View katmanı için `Smarty` ya da `Twig` gibi bağımsız bir şekilde kullanılabilen Template Engine'ler tavsiye edilmektedir.

Örnek SmartyService.php `smarty/smarty`
```php
<?php namespace App\Service;

use Smarty;

class SmartyService
{
    /**
     * @var Smarty
     */
    protected $smarty;

    public function __construct()
    {
        $this->smarty = new Smarty();
    }

    public function fetch($template, array $params = [])
    {
        return $this->smarty
            ->assign($params)
            ->fetch($template);
    }
}
```

Örnek TwigService.php `twig/twig`

```php
<?php namespace App\Service;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigService
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(ROOT_DIR . '/views/twig');

        $this->twig = new Environment($loader, [
            'cache' => ROOT_DIR . '/views/twig_c',
        ]);
    }

    public function render($name, array $context = [])
    {
        return $this->twig->render($name, $context);
    }
}
```

## Slimframework'den Karmaphp'ye Geçiş

Bu kısımda, mevcut bir projenizde slimframework kullanıyorsanız karmaphp'ye nasıl geçeceğiniz hakkında bilgiler  verilecek.

 * Controller fonksiyonlarında $request ve $response kullanmanıza gerek yok. Bunun yerine Controller içinde $this->request ve $this->response kullanabilirsiniz.
 * HandleError için kendi Exception sınıflarınızı yazarak `\Karma\ErrorHandler` sınıfını extend ederek invokable bir sınıf ile error handling lojiğinizi geliştirebilirsiniz.
 * ErrorHandling, Middleware ve Controller içinde $request ve $response kullanmanıza gerek yok. Container üzerinden ya da doğrudan Controller üzerinden $container->request ya da $this->request yazarak Request ve Response objelerine erişebilirsiniz.
 * Route tanımlarında ikinci parametreyi array vererek, birinci parametresi kendi Controller sınıfınız ikinci parametre olarak da içinde kullanmak istediğiniz fonksiyon adını yazabilirsiniz. Ör: `[MyController.class, 'Edit']`
 * Request parametrelerini almak için Controller içindeyken `$this->param('customer_id')` fonksiyonunu kullanabilirsiniz.
 * Gelen requestin POST olduğunu Controller içindeyken `$this->isPost()` fonksiyonu ile test edebilirsiniz.

## Veritabanı
Veritabanı işlemleri için orm olarak `illuminate/database` paketi önerilmektedir. Table sınıfları için `app/Table` Repo sınıfları için `app/Repo` klasörü önerilmektedir.

## Servisler
İhtiyaç duyulan servisler için `app\Service` klasörü önerilmektedir.

## Demo
https://github.com/karmaphp/demo adresindeki projeyi inceleyebilirsiniz.

## Krediler

Krediler Özgür Yazılım dünyasına emeklerini esirgemeyen herkese gelsin.

## Katkı Vermek İçin

Kullanın, kullanmayanlara önerin :) Önerileriniz olursa issue açabilirsiniz.
