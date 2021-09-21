<?php


namespace App\Services\AddMoneyCebuana;


interface IAddMoneyCebuanaService
{
    public function addMoney($userId, array $data);
}
