<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar sticky-top" style="background-color: darkgreen">
        <div class="container-fluid">
            <a class="navbar-brand fs-7 text-white">
                <img src="{{ asset('ndmulogo.png') }}" alt="Logo" alt="Logo" width="30" height="30" class="d-inline-block align-text-top" />
                Notre Dame of Marbel University
            </a>
            <span class="navbar-text text-white">
               Voucher Account Dispenser
            </span>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-5">
        <h1 class="mb-4">Student Information</h1>
        
        <form>
            <div class="mb-3">
                <label for="courseSelect" class="form-label">Select Course</label>
                <select class="form-select" id="courseSelect">
                    @foreach ($courses as $course)
                        <option value="{{ $course->code }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            
            
            <div class="mb-3">
                <label for="idNumber" class="form-label">ID Number</label>
                <input type="text" class="form-control form-control-sm" id="idNumber" placeholder="Enter your ID number" style="width: 200px;">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control form-control-sm" id="password" placeholder="Enter your password" style="width: 200px;">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </main>

    <!-- Footer -->
    <footer>
        NDMU © 2024 | Developed by MIS Department
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


