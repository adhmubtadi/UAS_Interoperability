<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    /**
     * Display a listing of barbers.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $barbers = Barber::all();
            
            return ResponseHelper::success($barbers, 'Data kapster berhasil diambil', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengambil data kapster: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created barber.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'status' => 'in:available,busy,off',
            'photo_url' => 'nullable|string|url'
        ]);

        try {
            $barber = Barber::create([
                'name' => $request->name,
                'status' => $request->status ?? 'available',
                'photo_url' => $request->photo_url,
            ]);

            return ResponseHelper::success($barber, 'Kapster berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal menambahkan kapster: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified barber.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $barber = Barber::find($id);

            if (!$barber) {
                return ResponseHelper::error('Kapster tidak ditemukan', null, 404);
            }

            return ResponseHelper::success($barber, 'Detail kapster berhasil diambil', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengambil detail kapster: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified barber.
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $this->validate($request, [
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:available,busy,off',
            'photo_url' => 'nullable|string|url'
        ]);

        try {
            $barber = Barber::find($id);

            if (!$barber) {
                return ResponseHelper::error('Kapster tidak ditemukan', null, 404);
            }

            $barber->update($request->only(['name', 'status', 'photo_url']));

            return ResponseHelper::success($barber, 'Kapster berhasil diupdate', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengupdate kapster: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update barber status only.
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi input
        $this->validate($request, [
            'status' => 'required|in:available,busy,off'
        ]);

        try {
            $barber = Barber::find($id);

            if (!$barber) {
                return ResponseHelper::error('Kapster tidak ditemukan', null, 404);
            }

            $barber->status = $request->status;
            $barber->save();

            return ResponseHelper::success($barber, 'Status kapster berhasil diupdate', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengupdate status kapster: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified barber.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $barber = Barber::find($id);

            if (!$barber) {
                return ResponseHelper::error('Kapster tidak ditemukan', null, 404);
            }

            $barber->delete();

            return ResponseHelper::success(null, 'Kapster berhasil dihapus', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal menghapus kapster: ' . $e->getMessage(), null, 500);
        }
    }
}
