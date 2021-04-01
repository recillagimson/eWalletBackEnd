<?php

namespace App\Services\NewsAndUpdate;

use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use Carbon\Carbon;

class NewsAndUpdateService implements INewsAndUpdateService
{
    private INewsAndUpdateRepository $newsAndUpdateRepository;

    public function __construct(INewsAndUpdateRepository $newsAndUpdateRepository)
    {
        $this->newsAndUpdateRepository = $newsAndUpdateRepository;
    }

    public function index() {
        $records = $this->newsAndUpdateRepository->getAll();

        return $records;
    }

    public function createRecord(array $details) {
        $inputBody = $this->inputBody($details);
        $createRecord = $this->newsAndUpdateRepository->create($inputBody);

        return $createRecord;
    }

    public function show(string $id) {
        $record = $this->newsAndUpdateRepository->get($id);

        return $record;
    }

    public function update(string $id, array $details) {
        $record = $this->newsAndUpdateRepository->get($id);
        $inputBody = $this->inputBody($details);
        $updateRecord = $this->newsAndUpdateRepository->update($record, $inputBody);

        return $updateRecord;
    }

    public function delete(string $id) {
        $record = $this->newsAndUpdateRepository->get($id);
        $deleteRecord = $this->newsAndUpdateRepository->delete($record);

        return $deleteRecord;
    }

    private function inputBody(array $details): array {
        $body = array(
                    'title'=>$details['title'],
                    'description'=>$details['description'],
                    'status'=>$details['status'] === 0 ? 0 : 1,
                    'image_location'=>$details['image_location'],
                );
        return $body;
    }

}
