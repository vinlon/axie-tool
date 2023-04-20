<?php

use Ndarproj\AxieGeneParser\AxieGene;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->make(\App\Console\Kernel::class)->bootstrap();

/* ============== Write Codes Below ================= */

/** @var \App\Services\AxieService $axieService */
$axieService = app()->get(\App\Services\AxieService::class);

/** @var \App\Services\RoninService $roninService */
$roninService = app()->get(\App\Services\RoninService::class);

/** @var \App\Services\GameService $gameService */
$gameService = app()->get(\App\Services\GameService::class);

$requirement = [
//    'back.d' => 'back-garish-worm',
    'back.r1' => 'back-garish-worm',
//    'back.r2' => 'back-garish-worm',
    'tail.d' => 'tail-cottontail',
    'ears.d' => 'ears-innocent-lamb',
];
$maxBreedCount = 1;
$maxMatchCount = 10;
$classes = ['Beast', 'Dawn'];
$url = "https://app.axieinfinity.com/marketplace/axies/?auctionTypes=Sale&breedCount=0&breedCount=" . $maxBreedCount;
foreach ($classes as $class) {
    $url .= '&classes=' . $class;
}
foreach ($requirement as $key => $value) {
    if (Str::endsWith($key, '.d')) {
        $url .= '&parts=' . $value;
    }
}
$loop = 0;
$size = 500;
$totalMatch = 0;
$done = false;
$matchResult = [];
while (true) {
    $matchCount = 0;
    echo '查询批次=' . $loop;
    $result = $axieService->listAxiesByMarketPlaceUrl($url, $loop * $size, $size);
    $loop++;
    $list = Arr::get($result, 'axies.results', []);
    echo ',结果数量=' . count($list);
    echo ',匹配中';
    foreach ($list as $index => $row) {
        if ($index % 50 == 0) {
            echo '.';
        }
        $geneStr = Arr::get($row, 'genes');
        $gene = new AxieGene($geneStr);
        $geneArr = $gene->getGenes();
        $match = true;
        foreach ($requirement as $part => $value) {
            if (Arr::get($geneArr, $part . '.partId') !== $value) {
                $match = false;
                break;
            }
        }
        if ($match) {
            $matchCount++;
            $totalMatch++;
            if ($totalMatch >= $maxMatchCount) {
                $done = true;
                break;
            }
            $matchResult[] = [
                Arr::get($row, 'class'),
                Arr::get($row, 'id'),
                toEth(Arr::get($row, 'order.currentPrice'), 6),
            ];
        }
    }
    echo '匹配数量=' . $matchCount . PHP_EOL;
    if (count($list) < $size) {
        break;
    }
    if ($done) {
        break;
    }
}
echo '匹配结果:' . PHP_EOL;
foreach ($matchResult as $row) {
    echo implode(' , ', $row) . PHP_EOL;
}
