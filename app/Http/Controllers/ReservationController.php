<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['table', 'customer'])
            ->when($request->filled('search'), function ($q) use ($request): void {
                $term = '%'.$request->input('search').'%';
                $q->where(function ($query) use ($term): void {
                    $query->where('guest_name', 'like', $term)->orWhere('phone', 'like', $term);
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('reservation_at', $request->input('date')));

        $reservations = $query->orderBy('reservation_at')->paginate(10)->withQueryString();

        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        return view('reservations.create', [
            'reservation' => new Reservation([
                'party_size' => 2,
                'duration_minutes' => 120,
                'status' => 'booked',
                'reservation_at' => now()->addHour()->startOfHour(),
            ]),
            'tables' => RestaurantTable::where('is_active', true)->orderBy('name')->get(),
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(ReservationRequest $request)
    {
        $data = $request->validated();
        $this->validatePartySize($data);

        $reservation = Reservation::create($data + ['user_id' => auth()->id()]);
        $this->syncTableStatus($reservation);

        return redirect()->route('reservations.index')->with('success', 'Reservation created successfully.');
    }

    public function edit(Reservation $reservation)
    {
        return view('reservations.edit', [
            'reservation' => $reservation,
            'tables' => RestaurantTable::where('is_active', true)->orderBy('name')->get(),
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(ReservationRequest $request, Reservation $reservation)
    {
        $data = $request->validated();
        $this->validatePartySize($data);

        $oldTableId = $reservation->table_id;
        $reservation->update($data);
        $this->releaseTableIfNeeded($reservation, $oldTableId);
        $this->syncTableStatus($reservation);

        return redirect()->route('reservations.index')->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        $this->releaseTableIfNeeded($reservation, $reservation->table_id);

        return redirect()->route('reservations.index')->with('success', 'Reservation cancelled.');
    }

    private function validatePartySize(array $data): void
    {
        if (! empty($data['table_id'])) {
            $table = RestaurantTable::find($data['table_id']);
            if ($table && $data['party_size'] > $table->capacity) {
                throw ValidationException::withMessages([
                    'party_size' => 'Party size cannot exceed the selected table capacity of '.$table->capacity.'.',
                ]);
            }
        }
    }

    private function syncTableStatus(Reservation $reservation): void
    {
        if (! $reservation->table_id) {
            return;
        }

        if ($reservation->status === 'seated') {
            RestaurantTable::whereKey($reservation->table_id)->update(['status' => 'occupied']);
        } elseif ($reservation->status === 'booked') {
            RestaurantTable::whereKey($reservation->table_id)->update(['status' => 'reserved']);
        }
    }

    private function releaseTableIfNeeded(Reservation $reservation, ?int $oldTableId): void
    {
        $tableId = $oldTableId ?: $reservation->table_id;
        if (! $tableId) {
            return;
        }

        $activeReservation = Reservation::where('table_id', $tableId)
            ->whereIn('status', ['booked', 'seated'])
            ->exists();
        $activeOrder = \App\Models\Order::where('table_id', $tableId)
            ->where('status', '!=', 'completed')
            ->exists();

        if (! $activeReservation && ! $activeOrder) {
            RestaurantTable::whereKey($tableId)->update(['status' => 'available']);
        }
    }
}
