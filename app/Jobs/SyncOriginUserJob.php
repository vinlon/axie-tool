<?php

namespace App\Jobs;

use App\Models\OriginUser;
use App\Services\AxieService;
use App\Services\RoninService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Scalar\String_;

class SyncOriginUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var String */
    private $userId;

    /** @var String */
    private $nickName;

    /** @var String */
    private $address;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $nickName, $address)
    {
        $this->userId = $userId;
        $this->nickName = $nickName;
        $this->address = $address;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AxieService $axieService, RoninService $roninService)
    {
        if (!empty($this->userId)) {
            $profile = $axieService->getProfileByUserId($this->userId);
        } else if (!empty($this->address)) {
            $profile = $axieService->getProfileByAddress($this->address);
        } else {
            return;
        }
        $address = \Arr::get($profile, 'addresses.ronin');
        $rnsName = $roninService->getRnsNameFromAddress($address);
        $user = OriginUser::where('ronin_address', $address)->first();
        if (!$user) {
            $user = new OriginUser();
        }
        $user->user_id = \Arr::get($profile, 'accountId');
        $user->profile_name = \Arr::get($profile, 'name');
        $user->rns_name = $rnsName;
        $user->ronin_address = $address;
        $user->nick_name = $this->nickName;
        $user->save();
    }
}
