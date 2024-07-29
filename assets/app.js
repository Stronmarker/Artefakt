// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);


import './styles/bootstrap.css';
import './styles/app.css';
import './bootstrap.js';
import './bootstrap.min.js'
import '@fortawesome/fontawesome-free/css/all.min.css';


import { Application } from "@hotwired/stimulus";
//import ThreeController from "./controllers/three-controller";


// Initialisation de l'application Stimulus
const application = Application.start();

// Enregistrement du contrôleur
//application.register("three", ThreeController);
//application.register("card-3d", Card3dController);

// Forcer refresh
// window.addEventListener("pageshow", function(event) {
//     if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
//         window.location.reload();
//     }
// });

