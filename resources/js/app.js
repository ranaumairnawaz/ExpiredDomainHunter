import './bootstrap';
import Swal from 'sweetalert2';

window.Swal = Swal;

window.addEventListener('swal', function(e) {
    Swal.fire(e.detail[0]);
});
