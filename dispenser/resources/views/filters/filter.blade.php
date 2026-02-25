{{-- resources/views/filters/filter.blade.php --}}
@extends('adminlte::page')

@section('title', 'Filter & Edit Student Credentials')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="mb-0" style="font-weight:900; color:#0B3D2E;">Filter & Edit Student Credentials</h1>
        <div class="text-muted small">Search students and manage credentials (Schoology / Kumosoft / SATP / Voucher).</div>
    </div>

    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
        <i class="fas fa-home"></i> Back to Home
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Global success toast --}}
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

    {{-- Filters --}}
    <form id="filter-form" method="GET" action="{{ route('filters.index') }}" class="card card-ndmu mb-3">
        <div class="card-body">
            <div class="row align-items-end">

                <div class="col-12 col-md-6 col-lg-4 mb-2">
                    <label class="form-label mb-1" style="font-weight:800;">Search (ID / Name)</label>
                    <input type="text"
                           name="q"
                           id="q"
                           value="{{ old('q', $q ?? '') }}"
                           class="form-control"
                           autocomplete="off"
                           placeholder="e.g. 20201234 or Dela Cruz">
                </div>

                <div class="col-12 col-md-6 col-lg-3 mb-2">
                    <label class="form-label mb-1" style="font-weight:800;">Course</label>
                    <input type="text"
                           name="course"
                           id="course"
                           value="{{ old('course', $course ?? '') }}"
                           class="form-control"
                           list="courseCodeList"
                           autocomplete="off"
                           placeholder="e.g. BSIT">
                    <datalist id="courseCodeList">
                        @foreach($courses as $c)
                            <option value="{{ $c->code }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="col-12 col-md-6 col-lg-3 mb-2">
                    <div class="form-check mt-2 mt-lg-4">
                        <input class="form-check-input" type="checkbox" name="only_with_creds" id="only_with_creds" {{ !empty($onlyWith) ? 'checked' : '' }}>
                        <label class="form-check-label" for="only_with_creds">
                            Show only students with any credentials
                        </label>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-2 mb-2">
                    <button type="submit" class="btn btn-ndmu w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>

            </div>
        </div>
    </form>

    {{-- Results --}}
    <div id="results">
        @include('filters.partials.results')
    </div>

</div>
@endsection

@push('css')
<style>
    :root{
        --ndmu-green:#0B3D2E;
        --ndmu-gold:#E3C77A;
        --paper:#FFFBF0;
    }

    .card-ndmu{
        border-radius:16px;
        border:2px solid rgba(11,61,46,.22);
        box-shadow:0 10px 24px rgba(0,0,0,.06);
        overflow:hidden;
    }

    .btn-ndmu{
        background: var(--ndmu-green);
        border-color: var(--ndmu-green);
        color:#fff;
        font-weight:900;
    }
    .btn-ndmu:hover{
        background:#082c21;
        border-color:#082c21;
        color:#fff;
    }

    .table th, .table td{
        vertical-align:middle;
        padding:.65rem .75rem;
        height:auto;
        white-space:nowrap;
        text-align:center;
    }
    .table td.text-left{ text-align:left !important; }

    .table-responsive{ overflow-x:auto !important; }

    #ajax-spinner{
        position:fixed;
        right:1rem;
        bottom:1rem;
        z-index:1051;
        display:none;
        box-shadow:0 6px 18px rgba(0,0,0,.12);
        border:1px solid rgba(11,61,46,.25);
    }

    #global-success{
        position:fixed;
        top:80px;
        right:20px;
        z-index:1055;
        min-width:320px;
        box-shadow:0 2px 8px rgba(0,0,0,0.15);
    }
</style>
@endpush

@push('js')
<script>
$(function () {

    function debounce(fn, d){ var t; return function(){ clearTimeout(t); t=setTimeout(fn.bind(this, ...arguments), d); }; }

    var $form   = $('#filter-form'),
        $q      = $('#q'),
        $course = $('#course'),
        $only   = $('#only_with_creds');

    function replaceResultsFromHtml(html){
        var $html = $('<div>').html(html),
            $new  = $html.find('#results');
        if ($new.length) $('#results').replaceWith($new);
    }

    function ensureSpinner(){
        if(!$('#ajax-spinner').length){
            $('body').append('<div id="ajax-spinner" class="btn btn-light"><i class="fas fa-spinner fa-spin"></i> Loading…</div>');
        }
    }

    function runSearch(push){
        var url = $form.attr('action') + '?' + $.param({
            q: $q.val(),
            course: $course.val(),
            only_with_creds: $only.is(':checked') ? 1 : ''
        });

        ensureSpinner();
        $('#ajax-spinner').fadeIn(80);

        $.get(url, function (data) {
            replaceResultsFromHtml(data);
        }).always(function(){
            $('#ajax-spinner').fadeOut(80);
        });

        if (push && history && history.pushState) history.pushState({},'',url);
    }

    $q.on('keyup', debounce(function(){ runSearch(true); }, 250));
    $course.on('keyup', debounce(function(){ runSearch(true); }, 250));
    $only.on('change', function(){ runSearch(true); });

    $form.on('submit', function(e){
        e.preventDefault();
        runSearch(true);
    });

    // Pagination via AJAX
    $(document).on('click', '#results .pagination a', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        if(!url) return;

        ensureSpinner();
        $('#ajax-spinner').fadeIn(80);

        $.get(url, function(data){
            replaceResultsFromHtml(data);
            if(history && history.pushState) history.pushState({},'',url);
        }).always(function(){ $('#ajax-spinner').fadeOut(80); });
    });

    // ===============================
    // MODAL SAVE (AJAX)  ✅ THIS FIXES "Save does nothing"
    // ===============================
    $(document).on('submit', '.ajax-cred-form', function(e){
        e.preventDefault();

        var $form = $(this);
        var sid   = $form.data('sid');
        var url   = $form.attr('action');

        var $fb = $form.find('.save-feedback');
        $fb.addClass('d-none').removeClass('alert-success alert-danger').text('');

        var $btn = $form.find('button[type="submit"]');
        var oldBtnHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving');

        $.ajax({
            url: url,
            method: 'POST',
            data: $form.serialize(),
            success: function(resp){
                if(!resp || resp.success !== true){
                    $fb.removeClass('d-none').addClass('alert alert-danger').text((resp && resp.message) ? resp.message : 'Save failed.');
                    return;
                }

                // Update row cells without refresh
                var $row = $('#row-' + sid);
                if($row.length && resp.row){

                    if(resp.row.course_code !== undefined){
                        $row.find('.cell-course').text(resp.row.course_code || '—');
                    }

                    if(resp.row.schoology_credentials !== undefined){
                        $row.find('.cell-schoology').text(resp.row.schoology_credentials || '—');
                    }

                    if(resp.row.kumosoft_school_id !== undefined){
                        $row.find('.cell-kumosoft-id').text(resp.row.kumosoft_school_id || '—');
                    }
                    if(resp.row.kumosoft_password !== undefined){
                        $row.find('.cell-kumosoft-pass').text(resp.row.kumosoft_password || '—');
                    }

                    if(resp.row.satp_password !== undefined){
                        $row.find('.cell-satp').text(resp.row.satp_password || '—');
                    }

                    if(resp.row.voucher_code !== undefined){
                        var v = resp.row.voucher_code || '';
                        $row.find('.cell-voucher').html(
                            v ? '<span class="badge badge-success voucher-code">' + v + '</span>' : '<span class="text-muted voucher-code">—</span>'
                        );
                    }

                    if(resp.row.birthday !== undefined){
                        $row.find('.cell-bday').text(resp.row.birthday || '');
                    }

                    if(resp.row.status_text !== undefined){
                        $row.find('.cell-status').html(
                            (resp.row.status_text === 'Active')
                                ? '<span class="badge badge-success">Active</span>'
                                : '<span class="badge badge-danger">Inactive</span>'
                        );
                    }
                }

                // Feedback
                $fb.removeClass('d-none').addClass('alert alert-success').text(resp.message || 'Changes saved.');

                // Global toast
                $('#global-success-text').text(resp.changed_text || 'Changes saved.');
                $('#global-success').removeClass('d-none').addClass('show');
                setTimeout(function () { $('#global-success').alert('close'); }, 2500);

                // Optional: close modal after short delay
                setTimeout(function(){
                    $('#editModal-' + sid).modal('hide');
                }, 350);

            },
            error: function(xhr){
                var msg = 'Server error.';
                try {
                    var j = JSON.parse(xhr.responseText);
                    if(j && j.message) msg = j.message;
                } catch(e){}

                $fb.removeClass('d-none').addClass('alert alert-danger').text(msg + ' (HTTP ' + xhr.status + ')');
            },
            complete: function(){
                $btn.prop('disabled', false).html(oldBtnHtml);
            }
        });
    });

    // ===============================
    // Voucher Generate (AJAX)
    // ===============================
    $(document).on('click', '.generate-voucher', function (e) {
        e.preventDefault();

        var $btn = $(this);
        var url  = $btn.data('url');
        var sid  = $btn.data('school-id');

        var token = $('meta[name="csrf-token"]').attr('content');
        if (!token) token = $('#editModal-' + sid).find('input[name="_token"]').val();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: token },
            success: function (resp) {
                if (!resp || resp.success !== true) {
                    alert((resp && resp.message) ? resp.message : 'Failed to generate voucher.');
                    return;
                }

                var code = resp.voucher_code || '';

                $('#voucher-display-' + sid).val(code);
                $('#voucher-' + sid).val(code);

                var $row = $('#row-' + sid);
                if ($row.length) {
                    $row.find('.cell-voucher').html(
                        code ? '<span class="badge badge-success voucher-code">' + code + '</span>' : '<span class="text-muted voucher-code">—</span>'
                    );
                }
            },
            error: function (xhr) {
                alert('Error generating voucher. (HTTP ' + xhr.status + ')');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-magic"></i> Generate');
            }
        });
    });

});
</script>
@endpush