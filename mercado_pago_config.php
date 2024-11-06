<?php
require_once 'vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;

// Autenticación de Mercado Pago
function authenticate() {
    $mpAccessToken = "TEST-8128019583936679-102919-50022028123c6ecc0ef63c40dd1f4872-383765822";
    MercadoPagoConfig::setAccessToken($mpAccessToken);
    //MercadoPagoConfig::setRuntimeEnvironment(MercadoPagoConfig::LOCAL); // Para pruebas en localhost
}
?>
