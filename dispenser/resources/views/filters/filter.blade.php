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
                <label class="form-label">Course</label>
                <input type="text" name="course" id="course" value="{{ old('course', $course ?? '') }}" class="form-control" list="courseCodeList" placeholder="">
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

    {{-- Results --}}
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
                                $justSavedForThis = (session('status') && (session('auto_open') ?? ($autoOpenId ?? null)) === $s->school_id);
                            @endphp
                            <tr>
                                <td class="font-weight-bold">{{ $s->school_id }}</td>
                                <td>{{ $s->lastname }}, {{ $s->firstname }} {{ $s->middlename }}</td>
                                <td class="d-none d-md-table-cell">{{ optional($s->course)->code }}</td>
                                <td class="d-none d-lg-table-cell">
                                    @if($email)
                                        <div>{{ $email->email_address }}</div>
                                        <div class="text-muted small">••••••</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="d-none d-xl-table-cell">{{ $schoology->schoology_credentials ?? '—' }}</td>
                                <td class="d-none d-xl-table-cell">{{ $kumo->kumosoft_credentials ?? '—' }}</td>
                                <td class="d-none d-lg-table-cell">{{ $satp->satp_password ?? '—' }}</td>
                                <td class="d-none d-lg-table-cell">
                                    @if($voucher)
                                        <span class="badge badge-success">{{ $voucher->voucher_code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary"
                                            data-toggle="modal"
                                            data-target="#editModal-{{ $s->school_id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>

                            {{-- Edit Modal (Bootstrap 4) --}}
                            <div class="modal fade" id="editModal-{{ $s->school_id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('filters.update', $s->school_id) }}">
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
                                                {{-- Success notice INSIDE this modal when coming back from a save --}}
                                                @if ($justSavedForThis)
                                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                        <strong>Changes saved.</strong>
                                                        <span class="ml-1">Successfully changed:
                                                            <strong>{{ session('changed_text') ?? (session('changed') ? implode(', ', session('changed')) : '—') }}</strong>
                                                        </span>
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                @endif

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

                                                    {{-- Birthday (editable) --}}
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
                                                                    data-school-id="{{ $s->school_id }}">
                                                                <i class="fas fa-magic"></i> Generate
                                                            </button>
                                                        </label>
                                                        <input type="text"
                                                               id="voucher-{{ $s->school_id }}"
                                                               name="voucher_code"
                                                               class="form-control"
                                                               value="{{ $voucher->voucher_code ?? '' }}"
                                                               placeholder="Click Generate"
                                                               readonly>
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
        right: 1rem;
        bottom: 1rem;
        z-index: 1051;
        display: none;
    }
</style>
@endpush

@push('js')
<script>
$(function () {
    // On successful save, re-open the SAME student's modal (to show inline success)
    @php $autoId = session('auto_open') ?? ($autoOpenId ?? null); @endphp
    @if (!empty($autoId))
        $('#editModal-{{ $autoId }}').modal('show');
    @endif

    // Debounce helper
    function debounce(fn, delay) {
        var t; return function() {
            var ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(function(){ fn.apply(ctx, args); }, delay);
        };
    }

    // Live search elements
    var $form   = $('#filter-form');
    var $q      = $('#q');
    var $course = $('#course');
    var $only   = $('#only_with_creds');

    function runSearch(pushState) {
        var url = $form.attr('action') + '?' + $.param({
            q: $q.val(),
            course: $course.val(),
            only_with_creds: $only.is(':checked') ? 1 : ''
        }) + '&ajax=1';

        if (!$('#ajax-spinner').length) {
            $('body').append('<div id="ajax-spinner" class="btn btn-light"><i class="fas fa-spinner fa-spin"></i> Loading…</div>');
        }
        $('#ajax-spinner').fadeIn(100);

        $.get(url, function (data) {
            var $html = $('<div>').html(data);
            var $newResults = $html.find('#results');
            if ($newResults.length) {
                $('#results').replaceWith($newResults);
            } else {
                $('#results').html($(data).find('table').closest('.card'));
            }
        }).always(function(){
            $('#ajax-spinner').fadeOut(100);
        });

        if (pushState && window.history && window.history.pushState) {
            window.history.pushState({}, '', $form.attr('action') + '?' + $.param({
                q: $q.val(),
                course: $course.val(),
                only_with_creds: $only.is(':checked') ? 1 : ''
            }));
        }
    }

    // Bind live search
    $q.on('keyup', debounce(function(){ runSearch(true); }, 250));
    $course.on('keyup', debounce(function(){ runSearch(true); }, 250));
    $only.on('change', function(){ runSearch(true); });

    // Fallback submit (uses same AJAX path)
    $form.on('submit', function(e){
        e.preventDefault();
        runSearch(true);
    });

    // CSRF for AJAX
    var csrf = '{{ csrf_token() }}';

    // Generate voucher (delegated so it works after AJAX refresh)
    $(document).on('click', '.generate-voucher', function () {
        var $btn = $(this);
        var schoolId = $btn.data('school-id');
        var $input = $('#voucher-' + schoolId);

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating…');

        $.ajax({
            url: "{{ route('filters.index') }}/" + encodeURIComponent(schoolId) + "/voucher/generate",
            type: "POST",
            data: { _token: csrf },
            success: function (res) {
                if (res && res.success) {
                    $input.val(res.voucher_code);
                    $('<div class="alert alert-success mt-2" role="alert">New voucher: <strong>' + res.voucher_code + '</strong></div>')
                        .insertAfter($input).delay(1500).fadeOut(400, function(){ $(this).remove(); });
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

});
</script>
@endpush
