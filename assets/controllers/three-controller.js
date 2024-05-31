import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        console.log("Le contrôleur Stimulus est connecté !");
    }
}
