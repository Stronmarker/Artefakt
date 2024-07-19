import { Controller } from '@hotwired/stimulus';
import Swal from 'sweetalert2';

export default class extends Controller {
    static targets = ['form'];

    connect() {
        // Sweetalert Supression Projet
        this.element.querySelectorAll(".delete-form").forEach((form) => {
            form.addEventListener("submit", (event) => {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false,
                    showCloseButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Your file has been deleted.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary w-xs mt-2',
                            },
                            buttonsStyling: false
                        }).then(() => {
                            form.submit(); // Submit the form after confirmation
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: 'Cancelled',
                            text: 'Your imaginary file is safe :)',
                            icon: 'error',
                            customClass: {
                                confirmButton: "btn btn-dark",
                            },
                            buttonsStyling: false
                        });
                    }
                });
            });
        });

        // Sweetalert registration form

        this.element.querySelectorAll(".form-signup").forEach((form) => {
            form.addEventListener("submit", (event) => {
                event.preventDefault();
                Swal.fire({
                    title: 'Success',
                    text: 'Pour accéder au site votre email doit-être vérifié. Vous avez reçu un lien par mail. Vous allez être redirigé vers une page de remerciment.',
                    icon: 'success',
                    confirmButtonText: 'Ok',
                    customClass: {
                        popup: 'my-swal-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Sweetalert change password

        this.element.querySelectorAll(".form-change-password").forEach((form) => {
            form.addEventListener("submit", (event) => {
                event.preventDefault();
                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Vous êtes sur le point de changer votre mot de passe.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, changer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }
}