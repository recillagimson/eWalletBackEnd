<?php


namespace App\Services\AddmoneyCebuana;


interface IAddMoneyCebuanaService
{
    public function addMoney($userId, array $data);
    public function generate(string $authUser, string $tierId);
    public function submit(array $attr);
}
