<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th style="min-width:130px;">School ID</th>
                    <th style="min-width:180px;">Name</th>
                    <th class="d-none d-md-table-cell" style="min-width:120px;">Course</th>
                    <th class="d-none d-lg-table-cell" style="min-width:200px;">Email</th>
                    <th class="d-none d-xl-table-cell" style="min-width:140px;">Schoology</th>
                    <th class="d-none d-xl-table-cell" style="min-width:140px;">Kumosoft</th>
                    <th class="d-none d-lg-table-cell" style="min-width:120px;">SATP</th>
                    <th class="d-none d-lg-table-cell" style="min-width:140px;">Voucher</th>
                    <th class="d-none d-lg-table-cell" style="min-width:140px;">Birthday</th>
                    <th class="text-center" style="width:90px;">Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($students as $s)
                    @php
                        $email     = $emails[$s->school_id] ?? null;
                        $satp      = $satps[$s->school_id] ?? null;
                        $kumo      = $kumos[$s->school_id] ?? null;
                        $schoology = $schoologys[$s->school_id] ?? null;
                        $voucher   = $s->voucher_id ? ($vouchers[$s->voucher_id] ?? null) : null;
                    @endphp
                    <tr id="row-{{ $s->school_id }}">
                        <td class="font-weight-bold">{{ $s->school_id }}</td>
                        <td>{{ $s->lastname }}, {{ $s->firstname }} {{ $s->middlename }}</td>
                        <td class="d-none d-md-table-cell">{{ optional($s->course)->code }}</td>
                        <td class="d-none d-lg-table-cell cell-email">
                            @if($email)
                                <div>{{ $email->email_address }}</div>
                                <div class="text-muted small">••••••</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="d-none d-xl-table-cell cell-schoology">{{ $schoology->schoology_credentials ?? '—' }}</td>
                        <td class="d-none d-xl-table-cell cell-kumosoft">{{ $kumo->kumosoft_credentials ?? '—' }}</td>
                        <td class="d-none d-lg-table-cell cell-satp">{{ $satp->satp_password ?? '—' }}</td>
                        <td class="d-none d-lg-table-cell cell-voucher">
                            @if($voucher)
                                <span class="badge badge-success">{{ $voucher->voucher_code }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="d-none d-lg-table-cell cell-bday">{{ optional(\Carbon\Carbon::parse($s->birthday))->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal-{{ $s->school_id }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </td>
                    </tr>

                    {{-- Modal --}}
                    <div class="modal fade" id="editModal-{{ $s->school_id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <form class="ajax-cred-form" method="POST" action="{{ route('filters.update', $s->school_id) }}" data-sid="{{ $s->school_id }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Edit Credentials — {{ $s->school_id }} ({{ $s->lastname }}, {{ $s->firstname }})
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="save-feedback alert d-none mb-3"></div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" name="email_address" class="form-control"
                                                       value="{{ $email->email_address ?? '' }}" placeholder="student@domain.edu">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email Password</label>
                                                <input type="text" name="email_password" class="form-control" value="">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Schoology Credentials</label>
                                                <input type="text" name="schoology_credentials" class="form-control"
                                                       value="{{ $schoology->schoology_credentials ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Kumosoft Credentials</label>
                                                <input type="text" name="kumosoft_credentials" class="form-control"
                                                       value="{{ $kumo->kumosoft_credentials ?? '' }}">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">SATP Password</label>
                                                <input type="text" name="satp_password" class="form-control"
                                                       value="{{ $satp->satp_password ?? '' }}">
                                            </div>

                        <div class="col-md-6">
                            <label class="form-label">Birthday</label>
                            <input type="date" name="birthday" class="form-control"
                                   value="{{ optional(\Carbon\Carbon::parse($s->birthday))->format('Y-m-d') }}">
                        </div>

                                            <div class="col-md-6">
                                                <label class="form-label d-flex align-items-center justify-content-between">
                                                    <span>Voucher Code</span>
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-sm generate-voucher"
                                                            data-url="{{ route('filters.voucher.generate', $s->school_id) }}"
                                                            data-school-id="{{ $s->school_id }}">
                                                        <i class="fas fa-magic"></i> Generate
                                                    </button>
                                                </label>
                                                <input type="text" id="voucher-display-{{ $s->school_id }}" class="form-control"
                                                       value="{{ $voucher->voucher_code ?? '' }}" placeholder="Click Generate" readonly>
                                                <input type="hidden" id="voucher-{{ $s->school_id }}" name="voucher_code" value="">
                                                <small class="form-text text-muted">Clicking <strong>Generate</strong> assigns the next available voucher and frees any old one automatically.</small>
                                            </div>

                        <div class="col-12">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="free_old_voucher" id="free-{{ $s->school_id }}">
                                <label class="form-check-label" for="free-{{ $s->school_id }}">Unassign current voucher (if any)</label>
                            </div>
                        </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No results. Try another filter.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        <div class="d-flex justify-content-center">
            {{ $students->onEachSide(1)->appends(request()->query())->links() }}
        </div>
        <div class="text-center small text-muted">
            Showing {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }} of {{ $students->total() }} results
        </div>
    </div>
</div>
