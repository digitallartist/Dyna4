<?php
// Works without composer, supports php 5.6
require 'binance-api-single.php';
$api = new Binance("xXhbCuQMiE13Rn67WXPUKPon8XuEEcnLbg0sdm8HNnMlhaeF0OEmFLDEmkunJK4G","nqE7AopmmuGkCcTsvNgqqIJuvNYW01qyLPLRLRKWGsxDDXjmliQ6DNAnXia5jQHk");
/*
$ticker = $api->prices();
print_r($ticker); // List prices of all symbols
echo "Price of BNB: {$ticker['BNBBTC']} BTC.".PHP_EOL;

// Get balances for all of your positions, including estimated BTC value
$balances = $api->balances($ticker);
print_r($balances);
echo "BTC owned: ".$balances['BTC']['available'].PHP_EOL;
echo "ETH owned: ".$balances['ETH']['available'].PHP_EOL;
echo "Estimated Value: ".$api->btc_value." BTC".PHP_EOL;

// Getting 24hr ticker price change statistics for a symbol
$prevDay = $api->prevDay("BNBBTC");
print_r($prevDay);
echo "BNB price change since yesterday: ".$prevDay['priceChangePercent']."%".PHP_EOL;
*/


$account = $api->account();
print_r($account);
