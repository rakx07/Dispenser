{{-- resources/views/filters/partials/results.blade.php --}}
<div id="results">
<div class="card card-ndmu">
    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table table-sm table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:130px;">School ID</th>
                        <th style="min-width:240px;">Name</th>
                        <th class="d-none d-md-table-cell" style="min-width:120px;">Course</th>

                        {{-- ✅ Email REMOVED from table --}}

                        <th class="d-none d-xl-table-cell" style="min-width:140px;">Schoology</th>
                        <th style="min-width:240px;">Kumosoft</th>
                        <th class="d-none d-lg-table-cell" style="min-width:120px;">SATP</th>
                        <th class="d-none d-lg-table-cell" style="min-width:140px;">Voucher</th>
                        <th class="d-none d-lg-table-cell" style="min-width:140px;">Birthday</th>
                        <th class="d-none d-lg-table-cell text-center" style="min-width:120px;">Status</th>
                        <th class="text-center col-action" style="width:90px; min-width:90px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($students as $s)
                    @php
                        $email     = $emails[$s->school_id] ?? null; // for modal only
                        $satp      = $satps[$s->school_id] ?? null;
                        $kumo      = $kumos[$s->school_id] ?? null;
                        $schoology = $schoologys[$s->school_id] ?? null;
                        $voucher   = $s->voucher_id ? ($vouchers[$s->voucher_id] ?? null) : null;
                    @endphp

                    <tr id="row-{{ $s->school_id }}">
                        <td class="font-weight-bold">{{ $s->school_id }}</td>

                        <td class="text-left">
                            <div style="font-weight:900; color:#0B3D2E;">
                                {{ $s->lastname }}, {{ $s->firstname }}
                            </div>
                            <div class="text-muted small">{{ $s->middlename }}</div>

                            <div class="d-md-none mt-2 small">
                                <div><b>Course:</b> <span class="cell-course">{{ optional($s->course)->code ?? '—' }}</span></div>
                                <div><b>Kumosoft ID:</b> <span class="cell-kumosoft-id">{{ $kumo->kumosoft_school_id ?? '—' }}</span></div>
                                <div><b>Kumosoft Pass:</b> <span class="cell-kumosoft-pass">{{ $kumo->password ?? '—' }}</span></div>
                            </div>
                        </td>

                        <td class="d-none d-md-table-cell cell-course">
                            {{ optional($s->course)->code ?? '—' }}
                        </td>

                        <td class="d-none d-xl-table-cell cell-schoology">
                            {{ $schoology->schoology_credentials ?? '—' }}
                        </td>

                        <td>
                            @if($kumo)
                                <div class="kumo-mini">
                                    <div class="top">
                                        <span class="badge badge-light"
                                              style="border:1px solid rgba(11,61,46,.25); color:#0B3D2E; font-weight:900;">
                                            ID
                                        </span>
                                        <span class="cell-kumosoft-id" style="font-weight:900; color:#0B3D2E;">
                                            {{ $kumo->kumosoft_school_id ?? '—' }}
                                        </span>
                                    </div>

                                    <div class="sub mt-1">
                                        <span class="badge badge-light"
                                              style="border:1px solid rgba(227,199,122,.65); color:#0B3D2E; font-weight:900;">
                                            PASS
                                        </span>
                                        <span class="cell-kumosoft-pass" style="font-weight:900; color:#111;">
                                            {{ $kumo->password ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td class="d-none d-lg-table-cell cell-satp">
                            {{ $satp->satp_password ?? '—' }}
                        </td>

                        <td class="d-none d-lg-table-cell cell-voucher">
                            @if($voucher)
                                <span class="badge badge-success voucher-code">{{ $voucher->voucher_code }}</span>
                            @else
                                <span class="text-muted voucher-code">—</span>
                            @endif
                        </td>

                        <td class="d-none d-lg-table-cell cell-bday">
                            {{ $s->birthday ? \Carbon\Carbon::parse($s->birthday)->format('Y-m-d') : '' }}
                        </td>

                        <td class="d-none d-lg-table-cell text-center cell-status">
                            @if((int)$s->status === 1)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>

                        <td class="text-center col-action">
                            <button type="button"
                                    class="btn btn-sm btn-ndmu btn-action"
                                    data-toggle="modal"
                                    data-target="#editModal-{{ $s->school_id }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </td>
                    </tr>

                    {{-- MODAL --}}
                    <div class="modal fade" id="editModal-{{ $s->school_id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <form class="ajax-cred-form"
                                      method="POST"
                                      action="{{ route('filters.update', $s->school_id) }}"
                                      data-sid="{{ $s->school_id }}">
                                    @csrf

                                    <div class="modal-header" style="background:linear-gradient(90deg, rgba(11,61,46,.10), rgba(227,199,122,.12));">
                                        <h5 class="modal-title" style="font-weight:900; color:#0B3D2E;">
                                            Edit Credentials — {{ $s->school_id }} ({{ $s->lastname }}, {{ $s->firstname }})
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="save-feedback alert d-none mb-3"></div>

                                        <div class="row">

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" style="font-weight:800;">Course / Program</label>
                                                <select name="course_id" class="form-control">
                                                    <option value="">— Select Course —</option>
                                                    @foreach($courses as $c)
                                                        <option value="{{ $c->id }}" {{ (int)$s->course_id === (int)$c->id ? 'selected' : '' }}>
                                                            {{ $c->code }} — {{ $c->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" style="font-weight:800;">Account Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ (int)$s->status === 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ (int)$s->status === 0 ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>

                                            {{-- EMAIL (modal only) --}}
                                            <div class="col-12 col-md-6 mt-3">
                                                <label class="form-label" style="font-weight:800;">Email Address</label>
                                                <input type="email" name="email_address" class="form-control"
                                                       value="{{ $email->email_address ?? '' }}" placeholder="student@ndmu.edu.ph">
                                            </div>
                                            <div class="col-12 col-md-6 mt-3">
                                                <label class="form-label" style="font-weight:800;">Email Password</label>
                                                <input type="text" name="email_password" class="form-control"
                                                    value="{{ old('email_password', $email->password ?? '') }}">
                                                <small class="text-muted">Edit if you want to change</small>
                                            </div>

                                            <div class="col-12 col-md-6 mt-3">
                                                <label class="form-label" style="font-weight:800;">Schoology Credentials</label>
                                                <input type="text" name="schoology_credentials" class="form-control"
                                                      value="{{ old('schoology_credentials', $schoology->schoology_credentials ?? '') }}">
                                            </div>

                                            <div class="col-12 col-md-6 mt-3">
                                                <label class="form-label" style="font-weight:800;">Kumosoft ID (kumosoft_school_id)</label>
                                                <input type="text" name="kumosoft_school_id" class="form-control"
                                                       value="{{ $kumo->kumosoft_school_id ?? '' }}" placeholder="Kumosoft ID">
                                            </div>

                                            <div class="col-12 col-md-6 mt-3">
                                                <label class="form-label" style="font-weight:800;">Kumosoft Password</label>
                                                <input type="text" name="kumosoft_password" class="form-control"
                                                    value="{{ old('kumosoft_password', $kumo->password ?? '') }}">
                                                <small class="text-muted">Edit if you want to change</small>
                                            </div>

                                            <div class="col-12 col-md-6 mt-3">
                                                <label class="form-label" style="font-weight:800;">Kumosoft Legacy (kumosoft_credentials)</label>
                                                <input type="text" name="kumosoft_credentials" class="form-control"
                                                       value="{{ $kumo->kumosoft_credentials ?? '' }}" placeholder="optional legacy string">
                                            </div>

                                            <div class="col-12 col-md-6 mt-3">
                                                <label class="form-label" style="font-weight:800;">SATP Password</label>
                                                <input type="text" name="satp_password" class="form-control"
                                                 value="{{ old('satp_password', $satp->satp_password ?? '') }}">
                                            </div>

                                            <div class="col-12 col-md-6 mt-3">
                                                <label class="form-label" style="font-weight:800;">Birthday</label>
                                                <input type="date" name="birthday" class="form-control"
                                                       value="{{ $s->birthday ? \Carbon\Carbon::parse($s->birthday)->format('Y-m-d') : '' }}">
                                            </div>

                                            <div class="col-12 col-md-6 mt-3">
                                                <label class="form-label d-flex align-items-center justify-content-between" style="font-weight:800;">
                                                    <span>Voucher Code</span>
                                                    <button type="button"
                                                            class="btn btn-outline-secondary btn-sm generate-voucher"
                                                            data-url="{{ route('filters.voucher.generate', $s->school_id) }}"
                                                            data-school-id="{{ $s->school_id }}">
                                                        <i class="fas fa-magic"></i> Generate
                                                    </button>
                                                </label>

                                                <input type="text"
                                                       id="voucher-display-{{ $s->school_id }}"
                                                       class="form-control"
                                                       value="{{ $voucher->voucher_code ?? '' }}"
                                                       placeholder="Click Generate"
                                                       readonly>

                                                <input type="hidden"
                                                       id="voucher-{{ $s->school_id }}"
                                                       name="voucher_code"
                                                       value="">

                                                <small class="form-text text-muted">
                                                    Clicking <strong>Generate</strong> assigns the next available voucher.
                                                </small>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="free_old_voucher" id="free-{{ $s->school_id }}">
                                                    <label class="form-check-label" for="free-{{ $s->school_id }}">
                                                        Unassign current voucher (if any)
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-ndmu">Save Changes</button>
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

<style>
.kumo-mini .top{
    font-weight:900;
    color:#0B3D2E;
    line-height:1.2;
}
.kumo-mini .sub{
    font-size:12px;
    color:#374151;
    line-height:1.2;
}
.table th.col-action,
.table td.col-action{
    width:90px !important;
    min-width:90px !important;
    max-width:90px !important;
    padding-left:.35rem !important;
    padding-right:.35rem !important;
}
.btn-action{
    font-size:.72rem !important;
    padding:.25rem .4rem !important;
    line-height:1.1 !important;
}
</style>
</div>