import { Controller } from '@hotwired/stimulus';
import Swal from 'sweetalert2';

export default class extends Controller {
    static targets = ['form'];

    connect() {
        this.showFlashMessages();

        // Sweetalert Supression
        this.element.querySelectorAll(".delete-form").forEach((form) => {
            form.addEventListener("submit", (event) => {
                event.preventDefault();
                Swal.fire({
                    title: 'Êtes-vous sûr(e) ?',
                    text: "Cette action est irréversible.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Non, annuler',
                    customClass: {
                        confirmButton: "btn btn-success me-2",  
                        cancelButton: "btn btn-danger ms-2"     
                    },
                    buttonsStyling: false,
                    showCloseButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Supprimé !',
                            text: 'Votre projet a été supprimé.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-dark w-xs mt-2',
                            },
                            buttonsStyling: false
                        }).then(() => {
                            form.submit(); // Soumettre le formulaire après confirmation
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: 'Annulé',
                            text: 'Votre projet est en sécurité.',
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

        //sweetalert d'édition
        this.element.querySelectorAll('.edit-form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Vous êtes sur le point d'enregistrer des modifications.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, enregistrer!',
                    cancelButtonText: 'Non, annuler',
                    customClass: {
                        confirmButton: "btn btn-success me-2",
                        cancelButton: "btn btn-danger ms-2"
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Modifications enregistrées!',
                            text: 'Modifications enregistrées avec succès.',
                            icon: 'success',
                            customClass: {
                                confirmButton: "btn btn-dark me-2",
                            },
                            confirmButtonText: 'OK'
                        }).then(() => {
                            form.submit();
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


        
        


        //sweetalert  modifications profil
        this.element.querySelectorAll('.profil-form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Vous êtes sur le point d'enregistrer les modifications.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, enregistrer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                        Swal.fire({
                            title: 'Modifications enregistrées!',
                            text: 'Vos informations ont été mises à jour avec succès.',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });

        //Change password

        this.element.querySelectorAll(".form-change-password").forEach((form) => {
            form.addEventListener("submit", (event) => {
                event.preventDefault();
                
                fetch(form.action, {
                    method: form.method,
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Succès',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '/dashboard'; // Redirection vers le tableau de bord
                        });
                    } else {
                        Swal.fire({
                            title: 'Erreur',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Erreur',
                        text: 'Votre ancien mot de passe est incorrect',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                });
            });
        });

    }

    showFlashMessages() {
        this.element.querySelectorAll(".flash-message").forEach((flashMessage) => {
            const label = flashMessage.getAttribute('data-label');
            const message = flashMessage.getAttribute('data-message');
            Swal.fire({
                icon: label === 'error' ? 'error' : 'success',
                title: label === 'error' ? 'Erreur' : 'Succès',
                text: message,
            });
            flashMessage.remove();
        });
    }
}


