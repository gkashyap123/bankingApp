<div class="mb-3">
    <label class="form-label">Customer</label>
    <select name="customer_id" class="form-select" required>
        <option value="">Select customer</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" {{ (old('customer_id', $event->customer_id ?? '') == $customer->id) ? 'selected' : '' }}>
                {{ $customer->name }}
            </option>
        @endforeach
    </select>
    @error('customer_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Type</label>
    <input type="text" name="type" class="form-control" value="{{ old('type', $event->type ?? '') }}" required>
    @error('type')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Event date</label>
    <input type="date" name="event_date" class="form-control" value="{{ old('event_date', isset($event) ? $event->event_date->format('Y-m-d') : '') }}" required>
    @error('event_date')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label class="form-label">Note</label>
    <textarea name="note" class="form-control">{{ old('note', $event->note ?? '') }}</textarea>
    @error('note')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="form-check mb-3">
    <input type="checkbox" name="notified" class="form-check-input" id="notified" {{ old('notified', $event->notified ?? false) ? 'checked' : '' }}>
    <label for="notified" class="form-check-label">Notified</label>
</div>
