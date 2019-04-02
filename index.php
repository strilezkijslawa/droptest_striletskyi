<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!defined('APP_PATH')) {
    define('APP_PATH', __DIR__ . '/');
}

require_once APP_PATH . 'controllers/Base_controller.php';
require_once APP_PATH . 'controllers/Products_controller.php';

$oProducts = new Products_controller();

echo $oProducts->countries_to_string() . '<br>';

$countries = $oProducts->countries();

if ($countries) {
    foreach ($countries as $country) {
        $country_products = $oProducts->products($country->id);

        echo "<div>{$country->name}</div>";
        if ($country_products) {
            foreach ($country_products as $product) {
                echo "<div>$product->name :: $product->count :: $product->price</div>";
            }
        }
    }
}

$product = $oProducts->product(1);
echo '<pre>'.print_r($product,true).'</pre>';