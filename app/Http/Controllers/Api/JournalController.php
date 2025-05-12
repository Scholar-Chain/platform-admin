<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\JournalRepository;

class JournalController extends Controller
{
    private $journalModel;

    public function __construct(JournalRepository $journalModel)
    {
        $this->journalModel = $journalModel;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $data['is_active'] = true;

            return $this->journalModel->all($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'errors' => 'Data not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'errors' => 'Proses data gagal, silahkan coba lagi.',
            ], $e->getCode() == 0 ? 500 : ($e->getCode() != 23000 ? $e->getCode() : 500));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $data = $this->journalModel->find($id);
            return response()->json($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'errors' => 'Data not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'errors' => 'Data process failed, please try again',
            ], $e->getCode() == 0 ? 500 : ($e->getCode() != 23000 ? $e->getCode() : 500));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
