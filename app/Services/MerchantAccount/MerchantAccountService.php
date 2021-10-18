<?php

namespace App\Services\MerchantAccount;

use Illuminate\Support\Str;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\MerchantAccount\IMerchantAccountRepository;
use App\Repositories\UserAccountNumber\IUserAccountNumberRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;

class MerchantAccountService implements IMerchantAccountService
{

    private IMerchantAccountRepository $merchantAccountRepo;
    private IUserAccountRepository $userAccountRepo;
    private IUserDetailRepository $userDetailRepo;
    private IUserAccountNumberRepository $userAccountNumbers;
    private IEmailService $emailService;
    private ISmsService $smsService;

    public function __construct(
        IMerchantAccountRepository $merchantAccountRepo,
        IUserAccountRepository $userAccountRepo,
        IEmailService $emailService,
        IUserDetailRepository $userDetailRepo,
        IUserAccountNumberRepository $userAccountNumbers,
        ISmsService $smsService
    )
    {
        $this->merchantAccountRepo = $merchantAccountRepo;
        $this->userAccountRepo = $userAccountRepo;
        $this->emailService = $emailService;
        $this->userDetailRepo = $userDetailRepo;
        $this->userAccountNumbers = $userAccountNumbers;
        $this->smsService = $smsService;
    }

    public function create(array $attr) {
        \DB::beginTransaction();
        try {
            // CREATE MERCHANT
            $merchant = $this->merchantAccountRepo->create($attr);
            $password = Str::random(16);
            $pinCode = rand(1000, 9999);
            $accountNumber = $this->userAccountNumbers->generateNo();
            $userAccount = $this->userAccountRepo->create([
                'email' => $attr['email'],
                'mobile_number' => $attr['mobile_number'],
                'password' => bcrypt($password),
                'pin_code' => bcrypt($pinCode),
                'user_created' => $attr['created_by'],
                'user_updated' => $attr['updated_by'],
                'account_number' => $accountNumber,
                'merchant_account_id' => $merchant->id
            ]);

            $userDetail = $this->userDetailRepo->create([
                'first_name' => $attr['first_name'],
                'last_name' => $attr['last_name'],
                'user_account_id' => $userAccount->id,
                'user_created' => $attr['created_by'],
                'user_updated' => $attr['updated_by']
            ]);

            $this->emailService->sendMerchantAccoutCredentials($userAccount->email, $userDetail->first_name, $password, $pinCode);
            $this->smsService->sendMerchantAccoutCredentials($userAccount->email, $userDetail->first_name, $password, $pinCode);

            \DB::commit();
            return [
                'merchant' => $merchant,
                'user_account' => $userAccount
            ];
        } catch (\Exception $e) {
            throw $e;
            \DB::rollBack();
        }
    }

    public function setUserMerchantAccount(array $attr) {
        \DB::beginTransaction();
        try {
            $userAccount = $this->userAccountRepo->get($attr['user_account_id']);
            $this->userAccountRepo->update($userAccount, [
                'merchant_account_id' => $attr['merchant_account_id']
            ]);
            $userAccount = $this->userAccountRepo->get($attr['user_account_id']);
            \DB::commit();
            return $userAccount;
        } catch(\Exception $e) {
            throw $e;
            \DB::rollBack();
        }
    }
}