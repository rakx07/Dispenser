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

    {{-- Global success prompt (hidden by default) --}}
    <div id="global-success" class="alert alert-success alert-dismissible fade d-none" role="alert">
        <strong>✅ Changes saved!</strong> <span class="ml-1" id="global-success-text"></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

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
                <input type="text" name="q" id="q" value="{{ old('q', $q ?? '') }}" class="form-control" autocomplete="off" placeholder="e.g. 20201234 or Dela Cruz">
            </div>
            <div class="col-sm-6 col-lg-3 mb-2">
                <label class="form-label">Course</label>
                <input type="text" name="course" id="course" value="{{ old('course', $course ?? '') }}" class="form-control" list="courseCodeList" autocomplete="off">
                <datalist id="courseCodeList">
                    @foreach($courses as $c)
                        <option value="{{ $c->code }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-sm-6 col-lg-3 mb-2">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="only_with_creds" id="only_with_creds" {{ !empty($onlyWith) ? 'checked' : '' }}>
                    <label class="form-check-label" for="only_with_creds">Show only students with any credentials</label>
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
                                        <div class="email-address">{{ $email->email_address }}</div>
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
                                        <span class="badge badge-success voucher-code">{{ $voucher->voucher_code }}</span>
                                    @else
                                        <span class="text-muted voucher-code">—</span>
                                    @endif
                                </td>
                                <td class="d-none d-lg-table-cell cell-bday">
                                    {{ $s->birthday ? \Carbon\Carbon::parse($s->birthday)->format('Y-m-d') : '' }}
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-primary"
                                       href="{{ route('filters.edit', $s->school_id) }}"
                                       target="_blank" rel="noopener">
                                        <i class="fas fa-external-link-alt"></i> Edit
                                    </a>
                                </td>
                            </tr>
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
    </div>

</div>
@endsection

@push('css')
<style>
.table th, .table td{ vertical-align:middle; text-align:center; padding:.75rem; height:60px; white-space:nowrap; }
@media (max-width:575.98px){ .table th, .table td{ white-space:normal; } }
#ajax-spinner{ position:fixed; right:1rem; bottom:1rem; z-index:1051; display:none; }
/* global success “toast” */
#global-success{ position:fixed; top:80px; right:20px; z-index:1055; min-width:300px; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
</style>
@endpush

@push('js')
<script>
$(function () {
    // ------- Live filter (updates only the #results card) -------
    function debounce(fn, d){ var t; return function(){ clearTimeout(t); t=setTimeout(fn.bind(this, ...arguments), d); }; }
    var $form=$('#filter-form'), $q=$('#q'), $course=$('#course'), $only=$('#only_with_creds');

    function replaceResultsFromHtml(html){
        var $html = $('<div>').html(html), $new = $html.find('#results');
        if ($new.length) $('#results').replaceWith($new);
    }
    function runSearch(push){
        var url = $form.attr('action') + '?' + $.param({
            q:$q.val(), course:$course.val(), only_with_creds:$only.is(':checked')?1:''
        });
        if(!$('#ajax-spinner').length){
            $('body').append('<div id="ajax-spinner" class="btn btn-light"><i class="fas fa-spinner fa-spin"></i> Loading…</div>');
        }
        $('#ajax-spinner').fadeIn(80);
        $.get(url, function (data) { replaceResultsFromHtml(data); })
         .always(function(){ $('#ajax-spinner').fadeOut(80); });
        if (push && history && history.pushState) history.pushState({},'',url);
    }
    $q.on('keyup',debounce(function(){ runSearch(true); },250));
    $course.on('keyup',debounce(function(){ runSearch(true); },250));
    $only.on('change',function(){ runSearch(true); });
    $form.on('submit',function(e){ e.preventDefault(); runSearch(true); });

    // ------- Pagination via AJAX -------
    $(document).on('click','#results .pagination a',function(e){
        e.preventDefault();
        var url=$(this).attr('href'); if(!url) return;
        if(!$('#ajax-spinner').length){
            $('body').append('<div id="ajax-spinner" class="btn btn-light"><i class="fas fa-spinner fa-spin"></i> Loading…</div>');
        }
        $('#ajax-spinner').fadeIn(80);
        $.get(url,function(data){
            replaceResultsFromHtml(data);
            if(history && history.pushState) history.pushState({},'',url);
        }).always(function(){ $('#ajax-spinner').fadeOut(80); });
    });

    // --- Listen for updates coming from the edit window (postMessage) ---
    window.addEventListener('message', function (ev) {
        if (!ev || !ev.data || ev.data.__filterUpdate !== true) return;
        var d = ev.data;
        var sid = d.sid;
        if (!sid) return;

        var $row = $('#row-' + sid);
        if (!$row.length) return;

        if (d.row) {
            if (d.row.email_address !== undefined) {
                $row.find('.cell-email').html(
                    d.row.email_address
                        ? '<div class="email-address">' + d.row.email_address + '</div><div class="text-muted small">••••••</div>'
                        : '<span class="text-muted">—</span>'
                );
            }
            if (d.row.schoology_credentials !== undefined) {
                $row.find('.cell-schoology').text(d.row.schoology_credentials || '—');
            }
            if (d.row.kumosoft_credentials !== undefined) {
                $row.find('.cell-kumosoft').text(d.row.kumosoft_credentials || '—');
            }
            if (d.row.satp_password !== undefined) {
                $row.find('.cell-satp').text(d.row.satp_password || '—');
            }
            if (d.row.voucher_code !== undefined) {
                var v = d.row.voucher_code || '';
                $row.find('.cell-voucher').html(
                    v ? '<span class="badge badge-success voucher-code">' + v + '</span>' : '<span class="text-muted voucher-code">—</span>'
                );
            }
            if (d.row.birthday !== undefined) {
                $row.find('.cell-bday').text(d.row.birthday || '');
            }
        }

        // Show the global success toast
        $('#global-success-text').text(d.changed_text || 'Changes saved.');
        $('#global-success').removeClass('d-none').addClass('show');
        setTimeout(function () {
            $('#global-success').alert('close');
        }, 4000);
    });

});
</script>
@endpush

