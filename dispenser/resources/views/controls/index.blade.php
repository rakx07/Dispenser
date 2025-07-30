@extends('adminlte::page')

@section('title', 'Credential Display Control')

@section('content_header')
    <h1>Credential Visibility Control Panel</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Toggle Credential Sections On/Off</h4>
    </div>

    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>Credential Section</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($settings as $setting)
                    <tr>
                        <td><strong>{{ ucfirst($setting->section) }}</strong></td>
                        <td>
                            <span class="badge {{ $setting->is_enabled ? 'badge-success' : 'badge-danger' }}">
                                {{ $setting->is_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm toggle-btn {{ $setting->is_enabled ? 'btn-danger' : 'btn-success' }}"
                                    data-section="{{ $setting->section }}">
                                {{ $setting->is_enabled ? 'Turn OFF' : 'Turn ON' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script>
    document.querySelectorAll('.toggle-btn').forEach(button => {
        button.addEventListener('click', function () {
            const section = this.getAttribute('data-section');

            fetch("{{ route('controls.toggle') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ section })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // reload to reflect toggle state
                }
            });
        });
    });
</script>
@endsection
