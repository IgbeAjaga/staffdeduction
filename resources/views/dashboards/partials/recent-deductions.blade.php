<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Deductions</h5>
                <a href="{{ route('deductions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Deduction ID</th>
                                <th>Staff Name</th>
                                <th>Branch</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\Deduction::with('branch')->latest()->take(8)->get() as $deduction)
                                @php
                                    $badge = match($deduction->status) {
                                        'pending'  => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'applied'  => 'info',
                                        default    => 'secondary',
                                    };
                                @endphp
                                <tr>
                                    <td><code>{{ $deduction->deduction_id }}</code></td>
                                    <td>{{ $deduction->staff_name }}</td>
                                    <td>{{ $deduction->branch?->name ?? '—' }}</td>
                                    <td>₦{{ number_format($deduction->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-light-{{ $badge }} text-{{ $badge }}">
                                            {{ ucfirst($deduction->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $deduction->deduction_date?->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('deductions.show', $deduction) }}"
                                           class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No deductions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>