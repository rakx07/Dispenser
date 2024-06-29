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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar sticky-top" style="background-color: darkgreen">
        <div class="container-fluid">
            <a class="navbar-brand fs-7 text-white">
                <img src="{{ asset('ndmulogo.png') }}" alt="Logo" alt="Logo" width="30" height="30" class="d-inline-block align-text-top" />
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
        
        
        <form>
            <div class="mb-3">
                <label for="courseSelect" class="form-label"><b>Select Your Course</b></label>
                <select class="form-select" id="courseSelect">
                    <option value=""><small>Select your course...</small></option><!-- Blank default option -->
                    @foreach ($courses as $course)
                        <option value="{{ $course->code }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            
            
            <div class="mb-3">
                <label for="idNumber" class="form-label"><b>ID Number</b></label>
                <input type="text" placeholder="Enter your ID number" class="form-control form-control-sm" id="idNumber" placeholder="Enter your ID number" style="width: 200px;">
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label"><b>Last Name</b></label>
                <input type="text" placeholder="Enter your Last name" class="form-control form-control-sm" id="idNumber" placeholder="Enter your ID number" style="width: 200px;">
            </div>
            <div class="mb-3">
                <label for="birthday" class="form-label"><b>Birthday<small> e.g. 2001-01-29 (2001-01-29)</small></b></label>
                <input type="text" placeholder="Enter your birtdhay" class="form-control form-control-sm" id="idNumber" placeholder="Enter your ID number" style="width: 200px;">
            </div>
            {{-- <div class="mb-3">
                <label for="password" class="form-label"><b>Password</b><br><small> e.g. Lastname2001-01-29 (Delacruz2001-01-29)</small></label>
                <input type="password" placeholder="Enter your password" class="form-control form-control-sm" id="password" placeholder="Enter your password" style="width: 200px;">
            </div> --}}
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


