<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Voucher Allocator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
        }
        footer {
            background-color: darkgreen;
            color: white;
            text-align: center;
            padding: 1rem;
        }
        .student-info {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .student-info h2 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar sticky-top" style="background-color: darkgreen">
        <div class="container-fluid">
            <a class="navbar-brand fs-7 text-white">
                <img src="{{ asset('ndmulogo.png') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top" />
                <b>Notre Dame of Marbel University</b>
            </a>
            <span class="navbar-text text-white">
               Voucher Account Allocator
            </span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-5">
        <h1 class="mb-4"><b>Student Information</b></h1>
    
        <!-- Student Information -->
        <div class="student-info">
            @if (isset($student))
                <h2>{{ $student->firstname }} {{ $student->middlename }} {{ $student->lastname }}</h2>
    
                <p><strong>ID Number: </strong>{{ $student->school_id }}</p>
    
                <p><strong>Course:</strong> {{ $student->course->name }}</p>
    
                <p><strong>Email:</strong> {{ $student->email_id }}</p>
    
                <p><strong>Temporary Email Password:</strong> TBA</p>
    
                <p><strong>Voucher Code:</strong> {{ $voucher->voucher_code }}</p>
            @else
                <div class="alert alert-danger">
                    Student information not found.
                </div>
            @endif
        </div>
    </main>
    

    <!-- Footer -->
    <footer>
        NDMU © 2024 | Developed by MIS Department
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
