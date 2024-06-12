// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);


import './styles/bootstrap.css';

import { Application } from "@hotwired/stimulus";
import ThreeController from "./controllers/three-controller";

// Initialisation de l'application Stimulus
const application = Application.start();

// Enregistrement du contrôleur
application.register("three", ThreeController);
