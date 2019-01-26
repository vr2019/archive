<?php

namespace App\Transformers\V1;

use League\Fractal\TransformerAbstract;
use App\Modal\V1\ArchiveModal;

class ArchiveTransformer extends TransformerAbstract
{
    public function transform(ArchiveModal $data) {
        return $data->attributesToArray();
    }
}