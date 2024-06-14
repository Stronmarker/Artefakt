import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    const button = document.querySelector('#alertButton'); // Modifiez l'ID en fonction de votre besoin
    if (button) {
      button.addEventListener('click', () => {
        Swal.fire({
          title: "Good job!",
          text: "You clicked the button!",
          icon: "success"
        });
      });
    }
  })