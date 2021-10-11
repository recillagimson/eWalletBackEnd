<?php


namespace App\Services\AddMoneyV2;


interface IAddMoneyService
{
    public function generateUrl(string $userId, array $data): array;

    public function handlePostBack(array $data);

    public function processPending(string $userId);
}
