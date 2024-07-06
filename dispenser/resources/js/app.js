import './bootstrap';

// Import Bootstrap
import 'bootstrap/dist/js/bootstrap.bundle';
import 'bootstrap/dist/css/bootstrap.min.css';

// Import DataTables
import 'datatables.net-bs5/js/dataTables.bootstrap5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

// Add this line if you're using Laravel Mix
require('./bootstrap');

// Initialize DataTables
$(document).ready(function() {
    $('.datatable').DataTable();
});
import Swal from 'sweetalert2';
window.Swal = Swal;