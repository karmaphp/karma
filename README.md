# Karma Framework
Mikro frameworklerden Slim Framework üzerine inşa edilmiş bir PHP Application Framework'tür.

## Misyon

PHP ile uygulama geliştirmeye alternatif bir çözüm önerisi sunmak. PHP'nin kaygan zemininden kaçarak Framework'lere sığınanlar için alternatif olmak.

## Vizyon

Ortaya koyulan standartlar ve önerileri herkesin benimseyerek kullanması ve önerilerle genelin ihtiyacını karşılayan bir yapı sunmak. 

## Composer ile Yükle

```json
{
  "require": {
    "karmaphp/karma": "dev-master"
  }
}
```

## Booting (index.php)

```php
require_once 'vendor/autoload.php';

$app = new \Karma\App();

$app->run();
```

## Container'ın Aktif Edilmesi

Karma Framework `php-di/php-di` paketi ile birlikte gelmektedir ve varsayılan container olarak **php-di** kullanmaktadır.

```php
require_once 'vendor/autoload.php';

$container = \App\Base\Container::build(
    // true olması durumunda çalışma zamanı hatalar sayfada gösterilir.
    'settings.displayErrorDetails' => true
);

$app = new \Karma\App($container);

$app->run();
```

## Rouing

Slim Framework routing özelliklerine ek olarak Çözümleme stratejisi olarak `[\App\Controller\MainController::class, 'Index']` şeklinde bir kullanımı mümkün kılmaktadır.

```php
<?php

require_once 'vendor/autoload.php';

$container = \Karma\Container::build(
    // true olması durumunda çalışma zamanı hatalar sayfada gösterilir.
    'settings.displayErrorDetails' => true
);

$app = new \Karma\App($container);

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

## View
View katmanı için `Smarty` ve `Twig` gibi bağımsız bir şekilde kullanılabilen Template Engine'ler tavsiye edilmektedir.

Örnek SmartyService.php `smarty/smarty`
```php
<?php namespace App\Service\View;

class SmartyService
{
    /**
     * @var \Smarty
     */
    private $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty();

        $this->smarty->setTemplateDir(ROOT_DIR . '/views/smarty');
        $this->smarty->setCompileDir(ROOT_DIR . '/views/smarty_c');
    }

    public function fetch($template, $params = [])
    {
        foreach ($params as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        return $this->smarty->fetch($template);
    }
}
```

Örnek TwigService.php `twig/twig`

```php
<?php namespace App\Service\View;

class TwigService
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(ROOT_DIR . '/views/twig');

        $this->twig = new \Twig_Environment($loader, [
            'cache' => ROOT_DIR . '/views/twig_c',
        ]);
    }

    public function render($name, array $context = [])
    {
        return $this->twig->render($name, $context);
    }
}
```

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
