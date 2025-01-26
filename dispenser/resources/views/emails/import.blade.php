<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Emails</title>
</head>
<body>
    <h1>Import Emails</h1>

    @if (session('status'))
        <p style="color: green;">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li style="color: red;">{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('emails.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="import_file">Upload Excel File:</label>
        <input type="file" name="import_file" id="import_file" required>
        <button type="submit">Import</button>
    </form>
</body>
</html>
