<?php

namespace App\Repositories\Eloquent;

use App\Models\Submission;
use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helpers\MediaFile as MediaHelpers;
use App\Repositories\Eloquent\BaseRepository;

class SubmissionRepository extends BaseRepository
{
    protected $model;

    public function __construct(Submission $model)
    {
        $this->model = $model;
    }
}
