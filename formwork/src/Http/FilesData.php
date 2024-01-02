<?php

namespace Formwork\Http;

use Formwork\Http\Files\UploadedFile;
use Formwork\Utils\Arr;

class FilesData extends RequestData
{
    public function isEmpty(): bool
    {
        if (parent::isEmpty()) {
            return true;
        }
        return Arr::every($this->getAll(), fn (UploadedFile $uploadedFile) => $uploadedFile->isEmpty());
    }

    /**
     * @return array<UploadedFile>
     */
    public function getAll(): array
    {
        $result = [];
        foreach ($this->data as $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $result[] = $v;
                }
            } else {
                $result[] = $value;
            }
        }
        return $result;
    }
}
