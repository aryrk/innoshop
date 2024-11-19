<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,

    InnoShop\Install\InstallServiceProvider::class,
    InnoShop\Common\CommonServiceProvider::class,
    InnoShop\Panel\PanelCCServiceProvider::class,
    InnoShop\Front\FrontServiceProvider::class,
    InnoShop\RestAPI\RestAPIServiceProvider::class,
    InnoShop\Plugin\PluginServiceProvider::class,
];
