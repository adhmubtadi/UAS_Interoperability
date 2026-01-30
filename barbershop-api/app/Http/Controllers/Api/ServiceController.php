<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Service::query();
            
            // Filter by name (case insensitive)
            if ($request->has('name') && $request->name != '') {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            
            // Filter by min_price
            if ($request->has('min_price') && $request->min_price != '') {
                $query->where('price', '>=', $request->min_price);
            }
            
            // Filter by max_price
            if ($request->has('max_price') && $request->max_price != '') {
                $query->where('price', '<=', $request->max_price);
            }
            
            $services = $query->get();
            
            return ResponseHelper::success($services, 'Data layanan berhasil diambil', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengambil data layanan: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created service.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string'
        ]);

        try {
            $service = Service::create([
                'name' => $request->name,
                'price' => $request->price,
                'duration_minutes' => $request->duration_minutes,
                'description' => $request->description,
            ]);

            return ResponseHelper::success($service, 'Layanan berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal menambahkan layanan: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified service.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return ResponseHelper::error('Layanan tidak ditemukan', null, 404);
            }

            return ResponseHelper::success($service, 'Detail layanan berhasil diambil', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengambil detail layanan: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified service.
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
            'price' => 'sometimes|required|numeric|min:0',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'description' => 'nullable|string'
        ]);

        try {
            $service = Service::find($id);

            if (!$service) {
                return ResponseHelper::error('Layanan tidak ditemukan', null, 404);
            }

            $service->update($request->only(['name', 'price', 'duration_minutes', 'description']));

            return ResponseHelper::success($service, 'Layanan berhasil diupdate', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengupdate layanan: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified service.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return ResponseHelper::error('Layanan tidak ditemukan', null, 404);
            }

            $service->delete();

            return ResponseHelper::success(null, 'Layanan berhasil dihapus', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal menghapus layanan: ' . $e->getMessage(), null, 500);
        }
    }
}
