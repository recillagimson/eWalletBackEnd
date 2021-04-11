<?php


namespace App\Repositories\OtpRepository;


use App\Models\Utilities\Otp;
use App\Repositories\Repository;
use Carbon\Carbon;

class OtpRepository extends Repository implements IOtpRepository
{
    /**
     * Delete old otps
     *
     * @var int
     */
    protected $deleteOldOtps;

    public function __construct(Otp $model)
    {
        parent::__construct($model);

        $this->deleteOldOtps = config('otp-generator.deleteOldOtps');
    }

    public function getByIdentifier(string $identifier, bool $validated = false)
    {
        return $this->model->where([
            'identifier' => $identifier,
            'validated' => $validated
        ])->first();
    }

    public function deleteOld()
    {
        $this->model->where('expired', true)
            ->orWhere('created_at', '<', Carbon::now()->subMinutes($this->deleteOldOtps))
            ->delete();
    }
}
