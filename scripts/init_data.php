<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->make(\App\Console\Kernel::class)->bootstrap();

/* ============== Write Codes Below ================= */
$arr = [
    '全部' => 'https://app.axieinfinity.com/marketplace/axies',
    'Kiss兽' => 'https://app.axieinfinity.com/marketplace/axies/?auctionTypes=Sale&parts=ears-belieber&parts=horn-little-branch&parts=eyes-little-peas&parts=tail-tiny-dino&parts=back-ronin&parts=mouth-axie-kiss',
    '流血兽' => 'https://app.axieinfinity.com/marketplace/axies/?auctionTypes=Sale&parts=horn-dual-blade&parts=mouth-axie-kiss&parts=tail-cottontail&parts=eyes-little-peas&parts=ears-innocent-lamb&parts=back-ronin',
];

\App\Models\QueryMonitor::truncate();
foreach ($arr as $name => $url) {
    $monitor = new \App\Models\QueryMonitor();
    $monitor->query_name = $name;
    $monitor->mp_query_url = $url;
    $monitor->duration = 5;
    $monitor->save();
}

\App\Models\AutoPurchase::query()->truncate();
$autoPurchase = new \App\Models\AutoPurchase();
$autoPurchase->query_monitor_id = 1;
$autoPurchase->max_purchase_count = 10;
$autoPurchase->max_purchase_price = 900000000000000;
$autoPurchase->save();
