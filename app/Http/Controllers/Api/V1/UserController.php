<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use PDOException;
use Throwable;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $users = User::query()->orderBy('id', 'asc')->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $users->isEmpty()
                    ? 'Tabel users saat ini masih kosong.'
                    : 'Berhasil menampilkan seluruh data user.',
                'total' => $users->count(),
                'data' => UserResource::collection($users),
            ], 200);
        } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => '500 Internal Server Error: Koneksi ke server database MySQL (db_grosiria) mati.',
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Terjadi kesalahan internal pada server.',
            ], 500);
        }
    }
}
