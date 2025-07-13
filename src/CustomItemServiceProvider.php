<?php

namespace Amplify\System\CustomItem;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class CustomItemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'../config/custom-item.php',
            'custom-item'
        );

        $pageTypes = Config::get('amplify.cms.page_types', []);

        $pageTypes[] = [
            'code' => 'custom_item_completed',
            'label' => 'Custom Item Completed',
            'description' => 'Confirmation Page for Custom item create request.',
            'middleware' => [],
            'reserved' => true,
            'url' => [
                'type' => 'route',
                'name' => 'frontend.custom-item.completed',
                'params' => '',
            ],
        ];

        Config::set('amplify.cms.page_types', $pageTypes);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'../resources/views', 'custom-item');

        $this->loadRoutesFrom(__DIR__.'../routes/web.php');

        $this->registerWidgets();
    }

    private function registerWidgets(): void
    {
        $widgets = [
            \Amplify\System\CustomItem\Widgets\Coil::class => [
                'name' => 'custom-item.coil',
                'reserved' => true,
                'internal' => false,
                'model' => ['custom_product'],
                '@inside' => null,
                '@client' => 'RHS',
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Custom evaporator coils create widget',
            ],
            \Amplify\System\CustomItem\Widgets\DrainTubeHeaters::class => [
                'name' => 'custom-item.drain-tube-heaters',
                'reserved' => true,
                'internal' => false,
                'model' => ['custom_product'],
                '@inside' => null,
                '@client' => 'RHS',
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Custom Drain tube heaters create widget',
            ],
            \Amplify\System\CustomItem\Widgets\CuttingBoard::class => [
                'name' => 'custom-item.cutting-board',
                'reserved' => true,
                'internal' => false,
                'model' => ['custom_product'],
                '@inside' => null,
                '@client' => 'RHS',
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Custom cutting board create widget',
            ],
            \Amplify\System\CustomItem\Widgets\Gasket::class => [
                'name' => 'custom-item.gasket',
                'reserved' => true,
                'internal' => false,
                'model' => ['custom_product'],
                '@inside' => null,
                '@client' => 'RHS',
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Custom molded gasket widget',
            ],
            \Amplify\System\CustomItem\Widgets\HeaterWire::class => [
                'name' => 'custom-item.heater-wire',
                'reserved' => true,
                'internal' => false,
                'model' => ['custom_product'],
                '@inside' => null,
                '@client' => 'RHS',
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Custom heater wire widget',
            ],
            \Amplify\System\CustomItem\Widgets\Shelving::class => [
                'name' => 'custom-item.shelving',
                'reserved' => true,
                'internal' => false,
                'model' => ['custom_product'],
                '@inside' => null,
                '@client' => 'RHS',
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Custom wire shelving widget',
            ],
            \Amplify\System\CustomItem\Widgets\StripCurtain::class => [
                'name' => 'custom-item.strip-curtain',
                'reserved' => true,
                'internal' => false,
                'model' => ['custom_product'],
                '@inside' => null,
                '@client' => 'RHS',
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Custom strip curtain widget',
            ],
            \Amplify\System\CustomItem\Widgets\TubularHeater::class => [
                'name' => 'custom-item.tubular-heater',
                'reserved' => true,
                'internal' => false,
                'model' => ['custom_product'],
                '@inside' => null,
                '@client' => 'RHS',
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Custom tubular heater widget',
            ],
            \Amplify\System\CustomItem\Widgets\DrainTubeHeater::class => [
                'name' => 'custom-item.drain-tube-heater',
                'reserved' => true,
                'internal' => false,
                'model' => ['custom_product'],
                '@inside' => null,
                '@client' => 'RHS',
                '@attributes' => [],
                '@nestedItems' => [],
                'description' => 'Custom drain tube heater widget',
            ],
        ];

        foreach ($widgets as $namespace => $options) {
            Config::set("amplify.widget.{$namespace}", $options);
        }
    }
}
