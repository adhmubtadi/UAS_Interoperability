<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Barber;
use App\Models\Service;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of user's bookings.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $bookings = Booking::with(['barber', 'service'])
                ->where('user_id', $user->id)
                ->orderBy('booking_date', 'desc')
                ->orderBy('booking_time', 'desc')
                ->get();
            
            return ResponseHelper::success($bookings, 'Riwayat booking berhasil diambil', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengambil riwayat booking: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created booking.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $this->validate($request, [
            'barber_id' => 'required|uuid|exists:barbers,id',
            'service_id' => 'required|uuid|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
        ]);

        try {
            $user = $request->user();
            
            // Cek ketersediaan kapster
            $barber = Barber::find($request->barber_id);
            
            if ($barber->status !== 'available') {
                return ResponseHelper::error(
                    'Kapster tidak tersedia. Status: ' . $barber->status,
                    null,
                    400
                );
            }
            
            // Cek bentrok jadwal
            $service = Service::find($request->service_id);
            $requestedTime = $request->booking_time;
            $serviceDuration = $service->duration_minutes;
            
            // Cek bentrok dengan booking yang sudah ada (yang belum canceled)
            $isConflict = $this->checkTimeConflict(
                $request->barber_id,
                $request->booking_date,
                $requestedTime,
                $serviceDuration
            );
            
            if ($isConflict) {
                return ResponseHelper::error(
                    'Jadwal bentrok! Kapster sudah ada booking di jam tersebut.',
                    null,
                    400
                );
            }
            
            // Buat booking baru
            $booking = Booking::create([
                'user_id' => $user->id,
                'barber_id' => $request->barber_id,
                'service_id' => $request->service_id,
                'booking_date' => $request->booking_date,
                'booking_time' => $requestedTime,
                'status' => 'pending',
            ]);
            
            // Load relasi untuk response
            $booking->load(['barber', 'service', 'user']);
            
            return ResponseHelper::success($booking, 'Booking berhasil dibuat', 201);
            
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal membuat booking: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified booking.
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $booking = Booking::with(['barber', 'service'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$booking) {
                return ResponseHelper::error('Booking tidak ditemukan', null, 404);
            }

            return ResponseHelper::success($booking, 'Detail booking berhasil diambil', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengambil detail booking: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Update booking status (for admin/barber).
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:pending,confirmed,completed,canceled'
        ]);

        try {
            $booking = Booking::with(['barber', 'service', 'user'])->find($id);

            if (!$booking) {
                return ResponseHelper::error('Booking tidak ditemukan', null, 404);
            }

            $booking->status = $request->status;
            $booking->save();

            return ResponseHelper::success($booking, 'Status booking berhasil diupdate', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengupdate status booking: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Check if the requested time conflicts with existing bookings.
     * 
     * @param string $barberId
     * @param string $bookingDate
     * @param string $requestedTime (format: H:i)
     * @param int $serviceDuration (in minutes)
     * @return bool
     */
    private function checkTimeConflict($barberId, $bookingDate, $requestedTime, $serviceDuration)
    {
        // Convert time string to minutes since midnight
        $requestedMinutes = $this->timeToMinutes($requestedTime);
        $requestedEndMinutes = $requestedMinutes + $serviceDuration;
        
        // Ambil semua booking barber di tanggal yang sama (exclude canceled)
        $existingBookings = Booking::with('service')
            ->where('barber_id', $barberId)
            ->where('booking_date', $bookingDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();
        
        foreach ($existingBookings as $existing) {
            $existingMinutes = $this->timeToMinutes($existing->booking_time);
            $existingEndMinutes = $existingMinutes + $existing->service->duration_minutes;
            
            // Cek apakah ada overlap
            // Overlap terjadi jika:
            // (requestedStart < existingEnd) AND (requestedEnd > existingStart)
            if ($requestedMinutes < $existingEndMinutes && $requestedEndMinutes > $existingMinutes) {
                return true; // Ada konflik
            }
        }
        
        return false; // Tidak ada konflik
    }

    /**
     * Convert time string (H:i) to minutes since midnight.
     * 
     * @param string $time
     * @return int
     */
    private function timeToMinutes($time)
    {
        list($hours, $minutes) = explode(':', $time);
        return ($hours * 60) + $minutes;
    }

    /**
     * Get available time slots for a barber on specific date.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSlots(Request $request)
    {
        $this->validate($request, [
            'barber_id' => 'required|uuid|exists:barbers,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'service_id' => 'required|uuid|exists:services,id',
        ]);

        try {
            $barber = Barber::find($request->barber_id);
            
            if ($barber->status !== 'available') {
                return ResponseHelper::error('Kapster tidak tersedia', null, 400);
            }
            
            $service = Service::find($request->service_id);
            $serviceDuration = $service->duration_minutes;
            
            // Jam operasional (misal: 09:00 - 21:00)
            $startHour = 9;
            $endHour = 21;
            
            $availableSlots = [];
            
            // Generate slot setiap 30 menit
            for ($hour = $startHour; $hour < $endHour; $hour++) {
                foreach ([0, 30] as $minute) {
                    $time = sprintf('%02d:%02d', $hour, $minute);
                    
                    // Cek apakah slot ini bentrok
                    $isConflict = $this->checkTimeConflict(
                        $request->barber_id,
                        $request->booking_date,
                        $time,
                        $serviceDuration
                    );
                    
                    if (!$isConflict) {
                        $availableSlots[] = $time;
                    }
                }
            }
            
            return ResponseHelper::success([
                'barber' => $barber,
                'service' => $service,
                'date' => $request->booking_date,
                'available_slots' => $availableSlots
            ], 'Jadwal tersedia berhasil diambil', 200);
            
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengambil jadwal tersedia: ' . $e->getMessage(), null, 500);
        }
    }
}
