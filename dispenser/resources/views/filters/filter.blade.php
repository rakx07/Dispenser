{{-- resources/views/filters/filter.blade.php --}}
@extends('adminlte::page')

@section('title', 'Filter & Edit Student Credentials')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Filter & Edit Student Credentials</h1>
    <a href="{{ route('home') }}" class="btn btn-secondary">
        <i class="fas fa-home"></i> Back to Home
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="font-weight-bold mb-1">Please fix the following:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Search / Filters --}}
    <form id="filter-form" method="GET" action="{{ route('filters.index') }}" class="card card-body mb-3">
        <div class="row align-items-end">
            <div class="col-sm-6 col-lg-4 mb-2">
                <label class="form-label">Search (ID / Name)</label>
                <input type="text" name="q" id="q" value="{{ old('q', $q ?? '') }}" class="form-control" placeholder="e.g. 20201234 or Dela Cruz">
            </div>
            <div class="col-sm-6 col-lg-3 mb-2">
                <label class="form-label">Course (code only)</label>
                <input type="text" name="course" id="course" value="{{ old('course', $course ?? '') }}" class="form-control" list="courseCodeList" placeholder="e.g., BSIT">
                <datalist id="courseCodeList">
                    @foreach($courses as $c)
                        <option value="{{ $c->code }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-sm-6 col-lg-3 mb-2">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="only_with_creds" id="only_with_creds"
                           {{ !empty($onlyWith) ? 'checked' : '' }}>
                    <label class="form-check-label" for="only_with_creds">
                        Show only students with any credentials
                    </label>
                </div>
            </div>
            <div class="col-sm-6 col-lg-2 text-right mb-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
    </form>

    {{-- Results (this wrapper gets replaced via AJAX) --}}
    <div id="results">
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
                            <tr id="row-{{ $s->school_id }}" data-sid="{{ $s->school_id }}">
                                <td class="font-weight-bold">{{ $s->school_id }}</td>
                                <td class="cell-name">{{ $s->lastname }}, {{ $s->firstname }} {{ $s->middlename }}</td>
                                <td class="cell-course d-none d-md-table-cell">{{ optional($s->course)->code }}</td>
                                <td class="cell-email d-none d-lg-table-cell">
                                    @if($email)
                                        <div class="email-address">{{ $email->email_address }}</div>
                                        <div class="text-muted small">••••••</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="cell-schoology d-none d-xl-table-cell">{{ $schoology->schoology_credentials ?? '—' }}</td>
                                <td class="cell-kumosoft d-none d-xl-table-cell">{{ $kumo->kumosoft_credentials ?? '—' }}</td>
                                <td class="cell-satp d-none d-lg-table-cell">{{ $satp->satp_password ?? '—' }}</td>
                                <td class="cell-voucher d-none d-lg-table-cell">
                                    @if($voucher)
                                        <span class="badge badge-success voucher-badge">{{ $voucher->voucher_code }}</span>
                                    @else
                                        <span class="text-muted voucher-badge">—</span>
                                    @endif
                                </td>
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
                                        <form method="POST"
                                              action="{{ route('filters.update', $s->school_id) }}"
                                              class="ajax-cred-form"
                                              data-school-id="{{ $s->school_id }}">
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
                                                {{-- Inline feedback appears here --}}
                                                <div class="save-feedback mb-3 d-none"></div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Email Address</label>
                                                        <input type="email" name="email_address" class="form-control"
                                                               value="{{ $email->email_address ?? '' }}" placeholder="student@domain.edu">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Email Password</label>
                                                        <input type="text" name="email_password" class="form-control"
                                                               value="{{ $email->password ?? '' }}" placeholder="">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Schoology Credentials</label>
                                                        <input type="text" name="schoology_credentials" class="form-control"
                                                               value="{{ $schoology->schoology_credentials ?? '' }}" placeholder="username / password or token">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Kumosoft Credentials</label>
                                                        <input type="text" name="kumosoft_credentials" class="form-control"
                                                               value="{{ $kumo->kumosoft_credentials ?? '' }}" placeholder="username / password">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">SATP Password</label>
                                                        <input type="text" name="satp_password" class="form-control"
                                                               value="{{ $satp->satp_password ?? '' }}" placeholder="">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Birthday</label>
                                                        <input type="date" name="birthday" class="form-control"
                                                               value="{{ $s->birthday ? \Carbon\Carbon::parse($s->birthday)->format('Y-m-d') : '' }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label d-flex align-items-center justify-content-between">
                                                            <span>Voucher Code</span>
                                                            <button type="button"
                                                                    class="btn btn-outline-secondary btn-sm generate-voucher"
                                                                    data-school-id="{{ $s->school_id }}">
                                                                <i class="fas fa-magic"></i> Generate
                                                            </button>
                                                        </label>
                                                        {{-- Display field (readonly) --}}
                                                        <input type="text"
                                                               id="voucher-display-{{ $s->school_id }}"
                                                               class="form-control"
                                                               value="{{ $voucher->voucher_code ?? '' }}"
                                                               placeholder="Click Generate"
                                                               readonly>
                                                        {{-- Hidden actual field (only set when generating or typing programmatically) --}}
                                                        <input type="hidden"
                                                               id="voucher-{{ $s->school_id }}"
                                                               name="voucher_code"
                                                               value="">
                                                        <small class="form-text text-muted">
                                                            Clicking <strong>Generate</strong> assigns the next available voucher and frees any old one automatically.
                                                        </small>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-check mt-2">
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
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- End Modal --}}
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No results. Try another filter.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination (keeps current filters) --}}
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $students->onEachSide(1)->appends(request()->query())->links() }}
                </div>
                <div class="text-center small text-muted">
                    Showing {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }} of {{ $students->total() }} results
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    .table th, .table td{
        vertical-align: middle;
        text-align: center;
        padding: .75rem;
        height: 60px;
        white-space: nowrap;
    }
    @media (max-width: 575.98px) {
        .table th, .table td { white-space: normal; }
    }
    #ajax-spinner {
        position: fixed;
        right: 1rem; bottom: 1rem;
        z-index: 1051; display: none;
    }
</style>
@endpush

@push('js')
<script>
$(function () {
    // Debounce helper
    function debounce(fn, delay) { let t; return function(){ clearTimeout(t); t = setTimeout(fn.bind(this, ...arguments), delay); }; }

    var $form   = $('#filter-form');
    var $q      = $('#q');
    var $course = $('#course');
    var $only   = $('#only_with_creds');
    var csrf    = '{{ csrf_token() }}';

    function replaceResultsFromHtml(html) {
        var $html = $('<div>').html(html);
        var $newResults = $html.find('#results');
        if ($newResults.length) {
            $('#results').replaceWith($newResults);
        } else {
            // fallback: just swap the first card found
            var $card = $html.find('.card').first();
            if ($card.length) $('#results').html($card);
        }
    }

    function showSpinner() {
        if (!$('#ajax-spinner').length) {
            $('body').append('<div id="ajax-spinner" class="btn btn-light"><i class="fas fa-spinner fa-spin"></i> Loading…</div>');
        }
        $('#ajax-spinner').fadeIn(100);
    }
    function hideSpinner(){ $('#ajax-spinner').fadeOut(100); }

    function currentQuery() {
        return {
            q: $q.val(),
            course: $course.val(),
            only_with_creds: $only.is(':checked') ? 1 : ''
        };
    }

    function runSearch(pushState) {
        var base = $form.attr('action');
        var qs   = currentQuery();
        var url  = base + '?' + $.param(qs) + '&ajax=1';

        showSpinner();
        $.get(url, function (data) {
            replaceResultsFromHtml(data);
        }).always(hideSpinner);

        if (pushState && window.history && window.history.pushState) {
            window.history.pushState({}, '', base + '?' + $.param(qs));
        }
    }

    // Live search (every letter)
    $q.on('keyup', debounce(function(){ runSearch(true); }, 250));
    $course.on('keyup', debounce(function(){ runSearch(true); }, 250));
    $only.on('change', function(){ runSearch(true); });
    $form.on('submit', function(e){ e.preventDefault(); runSearch(true); });

    // Intercept pagination clicks (AJAX paginate the table area only)
    $(document).on('click', '#results .pagination a', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (!url) return;
        showSpinner();
        $.get(url + (url.indexOf('?') >= 0 ? '&' : '?') + 'ajax=1', function (data) {
            replaceResultsFromHtml(data);
            if (window.history && window.history.pushState) {
                var cleanUrl = url.replace(/[?&]ajax=1\b/, '');
                window.history.pushState({}, '', cleanUrl);
            }
        }).always(hideSpinner);
    });

    // Generate voucher (delegated)
    $(document).on('click', '.generate-voucher', function () {
        var $btn = $(this);
        var sid  = $btn.data('school-id');
        var $display = $('#voucher-display-' + sid);
        var $hidden  = $('#voucher-' + sid);

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating…');

        $.ajax({
            url: "{{ route('filters.index') }}/" + encodeURIComponent(sid) + "/voucher/generate",
            type: "POST",
            data: { _token: csrf },
            success: function (res) {
                if (res && res.success) {
                    $display.val(res.voucher_code);
                    $hidden.val(res.voucher_code);
                    $('<div class="alert alert-success mt-2" role="alert">New voucher: <strong>' + res.voucher_code + '</strong></div>')
                        .insertAfter($display).delay(1500).fadeOut(400, function(){ $(this).remove(); });
                } else {
                    alert(res && res.message ? res.message : 'Failed to generate voucher.');
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error.';
                alert(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-magic"></i> Generate');
            }
        });
    });

    // AJAX submit for each modal form (delegated)
    $(document).on('submit', '.ajax-cred-form', function (e) {
        e.preventDefault();

        var $form   = $(this);
        var url     = $form.attr('action');
        var $modal  = $form.closest('.modal');
        var $footer = $modal.find('.modal-footer');
        var $btn    = $footer.find('button[type="submit"]');
        var $fb     = $form.find('.save-feedback');
        var sid     = $form.data('school-id');
        var formData = $form.serialize();

        // reset feedback
        $fb.removeClass('d-none alert alert-success alert-danger').empty();

        // disable button while saving
        $btn.prop('disabled', true).data('orig', $btn.html())
            .html('<i class="fas fa-spinner fa-spin"></i> Saving…');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function (res) {
                if (res && res.success) {
                    // Success message inside modal
                    var changedTxt = res.changed_text || 'Saved.';
                    var msg = (changedTxt.indexOf('No fields changed') === 0)
                        ? 'No fields changed.'
                        : 'Successfully changed: <strong>' + changedTxt + '</strong>';
                    $fb.addClass('alert alert-success').html('<strong>Changes saved.</strong> ' + msg);

                    // Update the row behind the modal
                    var snap = res.snapshot || {};
                    var $row = $('#row-' + sid);

                    if (snap.email_address !== undefined) {
                        var $cell = $row.find('.cell-email');
                        if (snap.email_address) {
                            $cell.html('<div class="email-address">'+snap.email_address+'</div><div class="text-muted small">••••••</div>');
                        } else {
                            $cell.html('<span class="text-muted">—</span>');
                        }
                    }
                    if (snap.schoology_credentials !== undefined) {
                        $row.find('.cell-schoology').text(snap.schoology_credentials || '—');
                    }
                    if (snap.kumosoft_credentials !== undefined) {
                        $row.find('.cell-kumosoft').text(snap.kumosoft_credentials || '—');
                    }
                    if (snap.satp_password !== undefined) {
                        $row.find('.cell-satp').text(snap.satp_password || '—');
                    }
                    if (snap.voucher_code !== undefined) {
                        var $vc = $row.find('.cell-voucher .voucher-badge');
                        if (snap.voucher_code) {
                            $row.find('.cell-voucher').html('<span class="badge badge-success voucher-badge">'+snap.voucher_code+'</span>');
                        } else {
                            $row.find('.cell-voucher').html('<span class="text-muted voucher-badge">—</span>');
                        }
                        // keep modal fields in sync
                        $('#voucher-' + sid).val(snap.voucher_code || '');
                        $('#voucher-display-' + sid).val(snap.voucher_code || '');
                    }
                    if (snap.birthday !== undefined) {
                        $form.find('[name="birthday"]').val(snap.birthday || '');
                    }

                    // Auto-close after a short delay (remove if you prefer to keep it open)
                    setTimeout(function(){ $modal.modal('hide'); }, 1200);

                } else {
                    $fb.addClass('alert alert-danger').text((res && res.message) ? res.message : 'Save failed.');
                }
            },
            error: function (xhr) {
                let msg = 'Save failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errs = xhr.responseJSON.errors;
                    msg = Object.values(errs).flat().join('<br>');
                }
                $fb.addClass('alert alert-danger').html(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html($btn.data('orig'));
            }
        });
    });
});
</script>
@endpush
