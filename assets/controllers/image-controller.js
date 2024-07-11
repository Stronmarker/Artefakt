import { Controller } from "@hotwired/stimulus";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls";

export default class extends Controller {
    static targets = ["canvasContainer", "pauseButton", "resetButton"];

    connect() {
        console.log("Le contrôleur Stimulus est connecté !");
        
        // Initialisation de la scène
        this.scene = new THREE.Scene();

        // Initialisation de la caméra
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.camera.position.set(0, 2, 10);

        // Initialisation du renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.canvasContainerTarget.appendChild(this.renderer.domElement);

        // Ajouter un fond avec une texture
        const loader = new THREE.TextureLoader();
        loader.load('path/to/your/background-image.jpg', (texture) => {
            this.scene.background = texture;
        });

        // Création du podium
        const podiumGeometry = new THREE.CylinderGeometry(2, 2, 1, 32);
        const podiumMaterial = new THREE.MeshPhongMaterial({ color: 0x555555 });
        const podium = new THREE.Mesh(podiumGeometry, podiumMaterial);
        this.scene.add(podium);

        // Ajout de lumières
        const ambientLight = new THREE.AmbientLight(0x404040, 2); // Lumière ambiante
        this.scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
        directionalLight.position.set(5, 10, 7.5);
        this.scene.add(directionalLight);

        // Ajout de la carte (forme 3D) sur le podium
        const frontTexture = new THREE.TextureLoader().load(this.element.dataset.imageFrontPng);
        const towardTexture = new THREE.TextureLoader().load(this.element.dataset.imageTowardPng);
        const whiteMaterial = new THREE.MeshBasicMaterial({ color: 0xffffff }); // Matériau blanc pour les bords

        const cardMaterials = [
            new THREE.MeshBasicMaterial({ map: frontTexture }),  // Face avant
            new THREE.MeshBasicMaterial({ map: towardTexture }), // Face arrière
            whiteMaterial, // Bordure supérieure
            whiteMaterial, // Bordure inférieure
            whiteMaterial, // Bordure gauche
            whiteMaterial  // Bordure droite
        ];
        const cardGeometry = new THREE.BoxGeometry(0.010, 2, 3.05); // Dimensions spécifiques de la carte
        this.card = new THREE.Mesh(cardGeometry, cardMaterials);
        this.card.position.y = 1.5; // Positionner la carte sur le podium
        this.scene.add(this.card);

        // Initialisation des contrôles pour bouger la carte
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.minPolarAngle = Math.PI / 6; // Empêcher la caméra de descendre trop bas
        this.controls.maxPolarAngle = Math.PI / 2; // Empêcher la caméra de monter trop haut
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.1;

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
        this.camera.position.set(0, 2, 10); // Réinitialiser la position de la caméra
        this.camera.lookAt(0, 1, 0); // Assurez-vous que la caméra regarde vers le podium
        this.controls.update(); // Mettre à jour les contrôles pour refléter les changements de la caméra
    }
}
