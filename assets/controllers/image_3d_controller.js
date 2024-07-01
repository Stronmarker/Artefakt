import { Controller } from "@hotwired/stimulus";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls";

export default class extends Controller {
    static targets = ["canvasContainer"];

    connect() {
        console.log("Le contrôleur Stimulus est connecté !");
        
        // Initialisation de la scène
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0x474747);

        // Initialisation de la caméra
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.camera.position.z = 5;
        console.log("Caméra initialisée");

        // Initialisation du renderer avec la correction gamma
        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(this.canvasContainerTarget.clientWidth, this.canvasContainerTarget.clientHeight);
        this.renderer.outputEncoding = THREE.sRGBEncoding; // Correction gamma
        this.canvasContainerTarget.appendChild(this.renderer.domElement);
        console.log("Renderer initialisé et ajouté au DOM");

        // Charger les images
        const frontPng = this.element.dataset.frontPng;
        const towardPng = this.element.dataset.towardPng;
        console.log("Front PNG URL:", frontPng);
        console.log("Toward PNG URL:", towardPng);

        // Vérifiez si les URLs des images sont définies
        if (!frontPng || !towardPng) {
            console.error("Les URLs des images ne sont pas définies:", { frontPng, towardPng });
            return;
        }

        // Ajout des images en 3D
        this.add3DImages(frontPng, towardPng);
        console.log("Images ajoutées en 3D");

        // Initialisation des contrôles
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        console.log("Contrôles de la caméra initialisés");

        // Fonction d'animation
        this.animate();

        // Ajuster la taille de la fenêtre
        window.addEventListener('resize', this.onWindowResize.bind(this));
        console.log("Événement de redimensionnement ajouté");
    }

    add3DImages(frontPng, towardPng) {
        const width = 3.5;
        const height = 2.3;
        const depth = 0.005; // Ajustez la profondeur pour plus de réalisme

        const geometry = new THREE.BoxGeometry(width, height, depth);
        const loader = new THREE.TextureLoader();

        const frontTexture = loader.load(frontPng, texture => {
            texture.encoding = THREE.sRGBEncoding; // Correction gamma
        });

        const towardTexture = loader.load(towardPng, texture => {
            texture.encoding = THREE.sRGBEncoding; // Correction gamma
        });

        const materials = [
            new THREE.MeshBasicMaterial({ color: 0xffffff }), // Bord droit
            new THREE.MeshBasicMaterial({ color: 0xffffff }), // Bord gauche
            new THREE.MeshBasicMaterial({ color: 0xffffff }),   // Bord haut
            new THREE.MeshBasicMaterial({ color: 0xffffff }),   // Bord bas
            new THREE.MeshBasicMaterial({ map: frontTexture }), // Face avant
            new THREE.MeshBasicMaterial({ map: towardTexture }) // Face arrière
        ];

        this.card = new THREE.Mesh(geometry, materials);
        this.scene.add(this.card);
        console.log("Card ajoutée à la scène");
    }

    animate() {
        requestAnimationFrame(this.animate.bind(this));

        // Rotation automatique de la carte
        this.card.rotation.y += 0;

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
