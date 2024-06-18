import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function() {
  document.querySelector(".confirm-button").addEventListener("click", function() {
      Swal.fire({
          title: "Merci de confirmer votre inscription",
          icon: "info",
          showCancelButton: true,
          confirmButtonText: "Confirmation",
          confirmButtonColor: "#ff0055",
          cancelButtonColor: "#999999",
          reverseButtons: true,
          focusConfirm: false,
          focusCancel: true
      }).then((result) => {
          if (result.isConfirmed) {
              document.getElementById("registrationForm").submit();
          }
      });
  });
});
