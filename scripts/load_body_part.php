<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->make(\App\Console\Kernel::class)->bootstrap();

/* ============== Write Codes Below ================= */

$version = '20231106';
$url = 'https://cdn.axieinfinity.com/game/origin-cards/base/origin-cards-data-20231106/part_data.json';

print_r(PHP_EOL);
print_r('版本:' . $version . PHP_EOL);
print_r('数据源:' . $url . PHP_EOL);
print_r('加载前数据条数为' . \App\Models\AxieBodyPart::count() . PHP_EOL);
\App\Models\AxieBodyPart::query()->where('version', $version)->delete();

$parts = Http::get($url)->json();
$batchValues = [];
$count = 0;
foreach ($parts as $part) {
    $batchValues[] = [
        'version' => $version,
        'origin_card_id' => Arr::get($part, 'originCard.id'),
        'part_id' => Arr::get($part, 'originCard.partId'),
        'part_type' => strtolower(Arr::get($part, 'originCard.partType')),
        'cls' => strtolower(Arr::get($part, 'originCard.partClass')),
        'part_name' => Arr::get($part, 'originCard.name'),
        'ability_type' => Arr::get($part, 'originCard.abilityType'),
        'description' => Arr::get($part, 'originCard.description'),
        'energy' => Arr::get($part, 'originCard.defaultEnergy'),
        'attack' => Arr::get($part, 'originCard.defaultAttack'),
        'defense' => Arr::get($part, 'originCard.defaultDefense'),
        'healing' => Arr::get($part, 'originCard.healing'),
        'special_genes' => Arr::get($part, 'originCard.specialGenes'),
        'created_at' => now(),
        'updated_at' => now(),
    ];
    $count++;
}

\App\Models\AxieBodyPart::query()->insert($batchValues);

print_r('加载后数据条数为' . \App\Models\AxieBodyPart::count() . PHP_EOL);
