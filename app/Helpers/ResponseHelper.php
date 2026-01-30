<?php

namespace App\Helpers;

class ResponseHelper
{
    /**
     * Return a standardized JSON success response
     *
     * @param mixed $data
     * @param string $pesan
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = null, string $pesan = 'Operasi berhasil', int $statusCode = 200)
    {
        return response()->json([
            'sukses' => true,
            'pesan' => $pesan,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Return a standardized JSON error response
     *
     * @param string $pesan
     * @param mixed $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(string $pesan = 'Operasi gagal', $data = null, int $statusCode = 400)
    {
        return response()->json([
            'sukses' => false,
            'pesan' => $pesan,
            'data' => $data
        ], $statusCode);
    }
}
