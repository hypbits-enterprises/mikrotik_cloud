{{-- Package, router and phone for selling a package on an M-Pesa payment (assign forms). --}}
<div class="form-group">
    <label for="assign_plan"><b>Package</b></label>
    <select class="form-control" name="plan_id" id="assign_plan" required>
        @foreach ($plans as $plan)
            <option value="{{ $plan->plan_id }}">{{ $plan->plan_name }} — Kes {{ number_format($plan->price) }}{{ $plan->is_active ? '' : ' (inactive)' }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="assign_router"><b>Router</b></label>
    <select class="form-control" name="router_id" id="assign_router" required>
        @foreach ($routers as $router)
            <option value="{{ $router->router_id }}">{{ $router->router_name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group mb-0">
    <label for="assign_phone"><b>Customer phone</b></label>
    <input class="form-control" type="text" name="phone" id="assign_phone" placeholder="e.g. 0712345678" required>
    <small class="text-muted">The ticket code is sent to this phone by SMS.</small>
</div>
