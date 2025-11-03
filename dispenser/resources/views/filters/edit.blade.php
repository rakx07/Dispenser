{{-- resources/views/filters/edit.blade.php --}}
@extends('adminlte::page')

@section('title', 'Edit Credentials — '.$student->school_id)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Edit Credentials — {{ $student->school_id }}</h1>
    <a href="{{ route('filters.index', ['q' => $student->school_id]) }}" class="btn btn-secondary">
        <i class="fas fa-list"></i> Back to List
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">

    <div id="page-feedback" class="alert d-none mb-3"></div>

    <div class="card">
        <div class="card-header">
            <div class="font-weight-bold">
                {{ $student->lastname }}, {{ $student->firstname }} {{ $student->middlename }}
            </div>
            <div class="text-muted small">
                Course: {{ optional($student->course)->code ?: '—' }}
            </div>
        </div>
        <div class="card-body">
            <form id="edit-form" method="POST" action="{{ route('filters.update', $student->school_id) }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email_address" class="form-control"
                               value="{{ $email->email_address ?? '' }}" placeholder="student@domain.edu">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Password</label>
                        <input type="text" name="email_password" class="form-control" value="">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Schoology Credentials</label>
                        <input type="text" name="schoology_credentials" class="form-control"
                               value="{{ $schoology->schoology_credentials ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kumosoft Credentials</label>
                        <input type="text" name="kumosoft_credentials" class="form-control"
                               value="{{ $kumo->kumosoft_credentials ?? '' }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">SATP Password</label>
                        <input type="text" name="satp_password" class="form-control"
                               value="{{ $satp->satp_password ?? '' }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Birthday</label>
                        <input type="date" name="birthday" class="form-control"
                               value="{{ $student->birthday ? \Carbon\Carbon::parse($student->birthday)->format('Y-m-d') : '' }}">
                    </div>

                    <div class="col-md-6 mb-1">
                        <label class="form-label d-flex align-items-center justify-content-between">
                            <span>Voucher Code</span>
                            <button type="button"
                                    class="btn btn-outline-secondary btn-sm"
                                    id="btn-generate"
                                    data-url="{{ route('filters.voucher.generate', $student->school_id) }}">
                                <i class="fas fa-magic"></i> Generate
                            </button>
                        </label>
                        <input type="text" id="voucher-display" class="form-control"
                               value="{{ $voucher->voucher_code ?? '' }}" placeholder="Click Generate" readonly>
                        <input type="hidden" id="voucher" name="voucher_code" value="">
                        <small class="form-text text-muted">Generate assigns the next available voucher and frees any old one.</small>
                    </div>

                    <div class="col-12">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="free_old_voucher" id="free-old">
                            <label class="form-check-label" for="free-old">Unassign current voucher (if any)</label>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-light" onclick="window.close()">Close Window</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('js')
<script>
(function(){
    var sid = @json($student->school_id);
    var csrf = @json(csrf_token());

    function showFeedback(ok, html){
        var $fb = $('#page-feedback');
        $fb.removeClass('d-none alert-success alert-danger')
           .addClass(ok ? 'alert alert-success' : 'alert alert-danger')
           .html(html);
        if (ok) setTimeout(function(){ $fb.addClass('d-none'); }, 4000);
    }

    // Save (AJAX)
    $('#edit-form').on('submit', function(e){
        e.preventDefault();
        var $btn = $('#btn-save');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving…');

        $.ajax({
            url: this.action,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json'
        }).done(function(res){
            if (res && res.success){
                showFeedback(true, '<strong>✅ Changes saved.</strong> <span class="ml-1">'+(res.changed_text || '')+'</span>');
                // Notify opener (parent) to update the row
                if (window.opener) {
                    window.opener.postMessage({
                        __filterUpdate: true,
                        sid: sid,
                        changed_text: res.changed_text || '',
                        row: res.row || {}
                    }, '*');
                }
                // Optional auto-close:
                // setTimeout(function(){ window.close(); }, 800);
            } else {
                showFeedback(false, res && res.message ? res.message : 'Save failed.');
            }
        }).fail(function(xhr){
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error.';
            showFeedback(false, msg);
        }).always(function(){
            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
        });
    });

    // Generate voucher (AJAX)
    $('#btn-generate').on('click', function(){
        var $b = $(this);
        $b.prop('disabled',true).html('<i class="fas fa-spinner fa-spin"></i> Generating…');

        $.ajax({
            url: $b.data('url'),
            type:'POST',
            data:{ _token: csrf },
            dataType:'json'
        }).done(function(res){
            if (res && res.success){
                $('#voucher-display').val(res.voucher_code);
                $('#voucher').val(res.voucher_code);
                showFeedback(true, '<strong>Voucher assigned:</strong> '+res.voucher_code);

                if (window.opener) {
                    window.opener.postMessage({
                        __filterUpdate: true,
                        sid: sid,
                        changed_text: 'Voucher (assigned): ' + res.voucher_code,
                        row: { voucher_code: res.voucher_code }
                    }, '*');
                }
            } else {
                showFeedback(false, res && res.message ? res.message : 'Failed to generate voucher.');
            }
        }).fail(function(xhr){
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error.';
            showFeedback(false, msg);
        }).always(function(){
            $b.prop('disabled',false).html('<i class="fas fa-magic"></i> Generate');
        });
    });
})();
</script>
@endpush
