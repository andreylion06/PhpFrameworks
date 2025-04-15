<div class="mb-3">
    <label>Order</label>
    <select name="order_id" class="form-control">
        @foreach ($orders as $order)
            <option value="{{ $order->id }}" {{ old('order_id', $orderItem->order_id ?? '') == $order->id ? 'selected' : '' }}>
                Order #{{ $order->id }} (Client: {{ $order->client->name }})
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Menu Item</label>
    <select name="menu_item_id" class="form-control">
        @foreach ($menuItems as $item)
            <option value="{{ $item->id }}" {{ old('menu_item_id', $orderItem->menu_item_id ?? '') == $item->id ? 'selected' : '' }}>
                {{ $item->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Quantity</label>
    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $orderItem->quantity ?? 1) }}">
</div>
