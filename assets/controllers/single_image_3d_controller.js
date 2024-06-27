import { Controller } from "@hotwired/stimulus";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls";

export default class extends Controller {
    static targets = ["canvasContainer"];

    connect() {
        console.log("Le contrôleur Stimulus est connecté !");
        
        // Initialisation de la scène
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0xffffff);

        // Initialisation de la caméra
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.camera.position.z = 5;
        console.log("Caméra initialisée");

        // Initialisation du renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(this.canvasContainerTarget.clientWidth, this.canvasContainerTarget.clientHeight);
        this.canvasContainerTarget.appendChild(this.renderer.domElement);
        console.log("Renderer initialisé et ajouté au DOM");

        // Charger l'image
        const frontPng = this.element.dataset.frontPng;
        console.log("Front PNG URL:", frontPng);

        // Vérifiez si l'URL de l'image est définie
        if (!frontPng) {
            console.error("L'URL de l'image n'est pas définie:", { frontPng });
            return;
        }

        // Ajout de l'image en 3D
        this.add3DImage(frontPng);
        console.log("Image ajoutée en 3D");

        // Initialisation des contrôles
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        console.log("Contrôles de la caméra initialisés");

        // Fonction d'animation
        this.animate();

        // Ajuster la taille de la fenêtre
        window.addEventListener('resize', this.onWindowResize.bind(this));
        console.log("Événement de redimensionnement ajouté");
    }

    add3DImage(imageUrl) {
        const width = 3.5;
        const height = 2;

        const geometry = new THREE.PlaneGeometry(width, height);
        const loader = new THREE.TextureLoader();
        const texture = loader.load(imageUrl);
        const material = new THREE.MeshBasicMaterial({ map: texture });

        this.image3D = new THREE.Mesh(geometry, material);
        this.scene.add(this.image3D);
        console.log("Image ajoutée à la scène");
    }

    animate() {
        requestAnimationFrame(this.animate.bind(this));

        // Rotation automatique de l'image
        this.image3D.rotation.y += 0.01;

        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }

    onWindowResize() {
        this.renderer.setSize(this.canvasContainerTarget.clientWidth, this.canvasContainerTarget.clientHeight);
        this.camera.aspect = this.canvasContainerTarget.clientWidth / this.canvasContainerTarget.clientHeight;
        this.camera.updateProjectionMatrix();
        console.log("Fenêtre redimensionnée");
    }
}
