<?php

namespace App\Http\Controllers\Api;

use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Repositories\Eloquent\JournalRepository;
use App\Http\Requests\Api\Submission\StoreRequest;

class SubmissionController extends Controller
{
    private $journalModel, $submissionModel;

    public function __construct(JournalRepository $journalModel)
    {
        $this->journalModel = $journalModel;
    }

    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $journal = $this->journalModel->find($data['journal_id']);

            $submission = Submission::create([
                'external_id' => $journal->external_id,
                'author_id' => auth('api')->user()->author->id,
                'publisher_id' => $journal->publisher_id,
            ]);

            DB::commit();
            return response()->json(new BaseResource($submission));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollback();
            return response()->json([
                'errors' => 'Data not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return response()->json([
                'errors' => 'Data process failed, please try again',
            ], $e->getCode() == 0 ? 500 : ($e->getCode() != 23000 ? $e->getCode() : 500));
        }
    }
}
