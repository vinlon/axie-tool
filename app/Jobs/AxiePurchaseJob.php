<?php

namespace App\Jobs;

use App\Enums\PurchaseStatus;
use App\Models\AutoPurchase;
use App\Models\AutoPurchaseRecord;
use App\Services\AxieService;
use App\Services\RoninService;
use Arr;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Str;
use Web3p\EthereumTx\Transaction;

class AxiePurchaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array $axie */
    private $axie;

    /** @var AutoPurchase */
    private $autoPurchase;
    private $marketContractAddress = '0xfff9ce5f71ca6178d3beecedb61e7eff1602950e';
    private $axieAddress = '32950db2a7164ae833121501c797d79e7b79d74c';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $axie, AutoPurchase $autoPurchase)
    {
        $this->axie = $axie;
        $this->autoPurchase = $autoPurchase;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RoninService $roninService)
    {
        $record = new AutoPurchaseRecord();
        $record->auto_purchase_id = $this->autoPurchase->id;
        $record->axie_id = Arr::get($this->axie, 'id');
        $record->owner = Arr::get($this->axie, 'owner', '');
        $record->price = Arr::get($this->axie, 'order.currentPrice', 0);
        $record->trans_hash = '';

        try {
            $walletPublicKey = config('services.wallet.public_key');
            $orderData = $this->buildOrderData($this->axie);
            $gasLimit = $roninService->estimateGas($orderData, $walletPublicKey, $this->marketContractAddress);
            $txData = [
                'from' => $walletPublicKey,
                'gasPrice' => 100 * 1000000000,
                'gasLimit' => hexdec($gasLimit),
                'to' => $this->marketContractAddress,
                'data' => $orderData,
                'nonce' => $this->getTransactionCount($roninService, $walletPublicKey),
                'chainId' => $this->getChainId($roninService),
            ];
            $tx = new Transaction($txData);
            $walletPrivateKey = config('services.wallet.private_key');
            $signedTx = $tx->sign($walletPrivateKey);
            $hash = $roninService->sendSignedTransaction($signedTx);
            \Cache::increment($this->getTransCountCacheKey($walletPublicKey));
            $record->trans_hash = $hash;
            $record->status = PurchaseStatus::WAITING;
            $record->save();
        } catch (Exception $e) {
            $record->status = PurchaseStatus::FAIL;
            $record->remark = $e->getMessage();
            $record->save();
        }
    }

    function buildOrderData($axie)
    {
        $paddingEnd = Str::repeat('0', 56);
        $preSig = Str::repeat('0', 24) . '8faf2b3f378d1ccb796b1e3adb1adf0a1a5e679d' . Str::repeat('0', 62) . 'a' . Str::repeat('0', 62) . '12' . Str::repeat('0', 63) . '41';
        $postSig = Str::repeat('0', 86);
        $orderSig = '344c54c66d3'; //使用WETH支付
        $order = Arr::get($axie, 'order');
        if (!$order) {
            throw new Exception('商品不是在售状态');
        }
        $currentPrice = $this->getUnitField($order, 'currentPrice');
        $basePrice = $this->getUnitField($order, 'basePrice');
        $signature = $this->getAddressField($order, 'signature');
        $divider = Str::repeat('0', 24);
        $dataField = [
            'maker' => $this->getAddressField($order, 'maker'),
            'kind' => $this->paduint(1) . $divider . '0000000000000000000000000000000000000180',
            'expiredAt' => $this->getUnitField($order, 'expiredAt'),
            'paymentToken' => $this->getAddressField($order, 'paymentToken'),
            'startedAt' => $this->getUnitField($order, 'startedAt'),
            'basePrice' => $basePrice,
            'endedAt' => $this->getUnitField($order, 'endedAt'),
            'endedPrice' => $this->getUnitField($order, 'endedPrice'),
            'exprectedState' => $this->paduint(0),
            'nonce' => $this->getUnitField($order, 'nonce'),
            'marketFeePercentage' => $this->getUnitField($order, 'marketFeePercentage'),
            'arrlen' => $this->paduint(1),
            'erc' => $this->paduint(1),
            'axieAddress' => '32950db2a7164ae833121501c797d79e7b79d74c',
            'assetId' => $this->paduint(Arr::get($axie, 'id')),
            'quantity' => $this->paduint(0),
        ];
        $result = '0x95a4ec0000000000000000000000000000000000000000000000000000000000000000400000000000000000000000000000000000000000000000000000000000000080000000000000000000000000000000000000000000000000000000000000000e4f524445525f45584348414e47450000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000';

        $result .= $orderSig;
        $result .= Str::repeat('0', 88);
        $result .= $currentPrice;
        $result .= $preSig;
        $result .= $signature;
        $result .= $postSig;
        $result .= implode($divider, array_values($dataField));
        $result .= $paddingEnd;
        return $result;
    }

    private function getTransactionCount($roninService, $walletPublicKey)
    {
        return \Cache::remember(
            $this->getTransCountCacheKey($walletPublicKey),
            60,
            function () use ($roninService, $walletPublicKey) {
                $transCount = $roninService->getTransactionCount($walletPublicKey);
                return hexdec($transCount);
            }
        );
    }

    private function getChainId(RoninService $roninService)
    {
        return \Cache::remember('ronin_chain_id', 60 * 60 * 24, function () use ($roninService) {
            return hexdec($roninService->getChainId());
        });
    }

    private function getTransCountCacheKey($walletPublicKey)
    {
        return 'transaction_count:' . $walletPublicKey;
    }


    private function paduint($number)
    {
        return str_pad(dechex($number), 40, '0', STR_PAD_LEFT);
    }

    private function getUnitField($arr, $key)
    {
        $value = Arr::get($arr, $key, 0);

        return $this->paduint($value);
    }

    private function getAddressField($arr, $key)
    {
        $value = Arr::get($arr, $key);

        return substr($value, 2);
    }

}
