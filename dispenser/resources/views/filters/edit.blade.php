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
                Current Course: {{ optional($student->course)->code ?: '—' }}
            </div>
        </div>

        <div class="card-body">
            <form id="edit-form" method="POST" action="{{ route('filters.update', $student->school_id) }}">
                @csrf
                <div class="row">

                    {{-- COURSE --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-control">
                            <option value="">— Select Course —</option>
                            @foreach(($courses ?? collect()) as $c)
                                <option value="{{ $c->id }}" {{ (int)$student->course_id === (int)$c->id ? 'selected' : '' }}>
                                    {{ $c->code }} — {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Changing this will update the student's course.</small>
                    </div>

                    {{-- BIRTHDAY --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Birthday</label>
                        <input type="date" name="birthday" class="form-control"
                               value="{{ $student->birthday ? \Carbon\Carbon::parse($student->birthday)->format('Y-m-d') : '' }}">
                    </div>

                    {{-- EMAIL ADDRESS --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email_address" class="form-control"
                               value="{{ $email->email_address ?? '' }}" placeholder="student@domain.edu">
                    </div>

                    {{-- EMAIL PASSWORD (VISIBLE TEXT) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Password</label>
                        <input type="text" name="email_password" class="form-control"
                               value="{{ $email->password ?? '' }}">
                        <small class="form-text text-muted">This will update the password in the email table.</small>
                    </div>

                    {{-- SCHOOLOGY --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Schoology Credentials</label>
                        <input type="text" name="schoology_credentials" class="form-control"
                               value="{{ $schoology->schoology_credentials ?? '' }}">
                    </div>

                    {{-- KUMOSOFT --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kumosoft Credentials</label>
                        <input type="text" name="kumosoft_credentials" class="form-control"
                               value="{{ $kumo->kumosoft_credentials ?? '' }}">
                    </div>

                    {{-- SATP --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SATP Password</label>
                        <input type="text" name="satp_password" class="form-control"
                               value="{{ $satp->satp_password ?? '' }}">
                    </div>

                    {{-- VOUCHER CODE + GENERATE BUTTON BELOW --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Voucher Code</label>
                        <input type="text" id="voucher-display" class="form-control mb-2"
                               value="{{ $voucher->voucher_code ?? '' }}" placeholder="Click Generate" readonly>
                        <input type="hidden" id="voucher" name="voucher_code" value="{{ $voucher->voucher_code ?? '' }}">

                        <button type="button"
                            class="btn btn-outline-secondary btn-sm w-100 mb-2"
                            id="btn-g-generate"
                            data-url="{{ route('filters.voucher.generate', $student->school_id) }}">
                            <i class="fas fa-magic"></i> Generate Voucher
                        </button>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="free_old_voucher" id="free-old">
                            <label class="form-check-label" for="free-old">Unassign current voucher (if any)</label>
                        </div>
                        <small class="form-text text-muted">Generate assigns the next available voucher and frees any old one.</small>
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
    var sid  = @json($student->school_id);
    var csrf = @json(csrf_token());

    function showFeedback(ok, html){
        var $fb = $('#page-feedback');
        $fb.removeClass('d-none alert-success alert-danger')
           .addClass(ok ? 'alert alert-success' : 'alert alert-danger')
           .html(html);
        if (ok) setTimeout(function(){ $fb.addClass('d-none'); }, 3500);
    }

    // Save AJAX
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
                showFeedback(true, '<strong>✅ Changes saved.</strong>');

                if (window.opener) {
                    window.opener.postMessage({ __filterUpdate: true, sid: sid }, '*');
                    window.opener.location.reload();
                }
                setTimeout(() => window.close(), 800);
            } else {
                showFeedback(false, res.message || 'Save failed.');
            }
        }).fail(function(){
            showFeedback(false, 'Server error.');
        }).always(function(){
            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
        });
    });

    // Voucher Generate Button
    $('#btn-g-generate').on('click', function(){
        var $b = $(this);
        $b.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating…');

        $.ajax({
            url: $b.data('url'),
            type: 'POST',
            data: { _token: csrf },
            dataType: 'json'
        }).done(function(res){
            if (res && res.success){
                $('#voucher-display').val(res.voucher_code);
                $('#voucher').val(res.voucher_code);
                showFeedback(true, '<strong>Voucher assigned:</strong> ' + res.voucher_code);
            } else {
                showFeedback(false, res.message || 'Error generating voucher.');
            }
        }).fail(function(){
            showFeedback(false, 'Server error.');
        }).always(function(){
            $b.prop('disabled', false).html('<i class="fas fa-magic"></i> Generate Voucher');
        });
    });

})();
</script>
@endpush
