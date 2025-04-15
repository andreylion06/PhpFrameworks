<div class="mb-3">
    <label>Client</label>
    <select name="client_id" class="form-control">
        @foreach ($clients as $client)
            <option value="{{ $client->id }}" {{ old('client_id', $tableReservation->client_id ?? '') == $client->id ? 'selected' : '' }}>
                {{ $client->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Table Number</label>
    <input type="number" name="table_number" class="form-control" value="{{ old('table_number', $tableReservation->table_number ?? '') }}">
</div>

<div class="mb-3">
    <label>Reservation Date</label>
    <input type="datetime-local" name="reservation_date" class="form-control" value="{{ old('reservation_date', isset($tableReservation) ? \Illuminate\Support\Carbon::parse($tableReservation->reservation_date)->format('Y-m-d\TH:i') : '') }}">
</div>
