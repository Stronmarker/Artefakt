import { Controller } from "@hotwired/stimulus";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls";

export default class extends Controller {
    static targets = ["canvasContainer", "pauseButton", "resetButton"];

    connect() {
        console.log("Le contrôleur Stimulus est connecté !");
        // Initialisation de la scène
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0x575d66);

        // Initialisation de la caméra
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.camera.position.z = 5;

        // Initialisation du renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.canvasContainerTarget.appendChild(this.renderer.domElement);

        // Ajout de la forme 3D
        const frontTexture = new THREE.TextureLoader().load(this.element.dataset.image3dFrontPng);
        const towardTexture = new THREE.TextureLoader().load(this.element.dataset.image3dTowardPng);
        const materials = [
            new THREE.MeshBasicMaterial({ map: frontTexture }),
            new THREE.MeshBasicMaterial({ map: towardTexture }),
            new THREE.MeshBasicMaterial({ map: frontTexture }),
            new THREE.MeshBasicMaterial({ map: frontTexture }),
            new THREE.MeshBasicMaterial({ map: towardTexture }),
            new THREE.MeshBasicMaterial({ map: frontTexture })
        ];
        const geometry = new THREE.BoxGeometry(3.5, 2, 0.1);
        this.card = new THREE.Mesh(geometry, materials);
        this.scene.add(this.card);

        // Initialisation des contrôles pour bouger la carte
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);

        // Variables pour gérer la rotation automatique
        this.autoRotate = true;

        // Fonction d'animation
        this.animate();

        // Ajuster la taille de la fenêtre
        window.addEventListener('resize', this.onWindowResize.bind(this));

        // Gérer les boutons
        this.pauseButtonTarget.addEventListener('click', this.toggleRotation.bind(this));
        this.resetButtonTarget.addEventListener('click', this.resetCamera.bind(this));
    }

    animate() {
        requestAnimationFrame(this.animate.bind(this));

        // Rotation automatique de la carte si autoRotate est true
        if (this.autoRotate) {
            this.card.rotation.y += 0.01;
        }

        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }

    onWindowResize() {
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.camera.aspect = window.innerWidth / window.innerHeight;
        this.camera.updateProjectionMatrix();
    }

    toggleRotation() {
        this.autoRotate = !this.autoRotate;
        this.pauseButtonTarget.textContent = this.autoRotate ? 'Pause Rotation' : 'Resume Rotation';
        this.pauseButtonTarget.style.backgroundColor = this.autoRotate ? '#f00' : '#0f0';
    }

    resetCamera() {
        this.camera.position.set(0, 0, 5); // Réinitialiser la position de la caméra
        this.camera.lookAt(0, 0, 0); // Assurez-vous que la caméra regarde vers le centre de la scène
        this.controls.update(); // Mettre à jour les contrôles pour refléter les changements de la caméra
    }
}
