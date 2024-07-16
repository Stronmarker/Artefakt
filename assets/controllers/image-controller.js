import { Controller } from "@hotwired/stimulus";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls";
import { Modal } from "bootstrap";

export default class extends Controller {
    static targets = ["canvasContainer", "resetButton", "pauseButton"];

    connect() {
        console.log("Le contrôleur Stimulus est connecté !");
        
        // Initialisation de la scène
        this.scene = new THREE.Scene();

        // Initialisation de la caméra
        this.updateCanvasSize(this.canvasContainerTarget); // Set initial size
        this.camera = new THREE.PerspectiveCamera(75, this.canvasWidth / this.canvasHeight, 0.1, 1000);
        this.camera.position.set(2, 2, 4);

        // Initialisation du renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(this.canvasWidth, this.canvasHeight);
        this.canvasContainerTarget.appendChild(this.renderer.domElement);

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

        // Chargement des textures et ajout de la carte (forme 3D) sur le podium
        this.loadTextures().then(() => {
            this.addCardToScene();
        });

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
        window.addEventListener('resize', () => this.onWindowResize(this.canvasContainerTarget));
    }

    async loadTextures() {
        this.frontTexture = await new Promise(resolve => {
            new THREE.TextureLoader().load(this.element.dataset.imageFrontPng, resolve);
        });
        this.towardTexture = await new Promise(resolve => {
            new THREE.TextureLoader().load(this.element.dataset.imageTowardPng, resolve);
        });
    }

    addCardToScene() {
        if (this.frontTexture && this.towardTexture) {
            const whiteMaterial = new THREE.MeshBasicMaterial({ color: 0xffffff }); // Matériau blanc pour les bords
            const cardMaterials = [
                new THREE.MeshBasicMaterial({ map: this.frontTexture }),  // Face avant
                new THREE.MeshBasicMaterial({ map: this.towardTexture }), // Face arrière
                whiteMaterial, // Bordure supérieure
                whiteMaterial, // Bordure inférieure
                whiteMaterial, // Bordure gauche
                whiteMaterial  // Bordure droite
            ];
            const cardGeometry = new THREE.BoxGeometry(0.010, 2, 3.05); // Dimensions spécifiques de la carte
            this.card = new THREE.Mesh(cardGeometry, cardMaterials);
            this.card.position.y = 1.5; // Positionner la carte sur le podium
            this.scene.add(this.card);
        }
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

    updateCanvasSize(target) {
        this.canvasWidth = target.clientWidth;
        this.canvasHeight = target.clientHeight;
    }

    onWindowResize(target) {
        this.updateCanvasSize(target);
        this.renderer.setSize(this.canvasWidth, this.canvasHeight);
        this.camera.aspect = this.canvasWidth / this.canvasHeight;
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

    showModalCanvas(event) {
        

        // Création de la modale
        const modalElement = document.createElement('div');
        modalElement.classList.add('modal', 'fade');
        modalElement.id = 'canvasModal';
        modalElement.setAttribute('tabindex', '-1');
        modalElement.setAttribute('aria-labelledby', 'canvasModalLabel');
        modalElement.setAttribute('aria-hidden', 'true');

        modalElement.innerHTML = `
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <button class="btn btn-secondary me-2" data-action="click->image#toggleRotation">Pause Rotation</button>
                            <button class="btn btn-secondary me-2" data-action="click->image#resetCamera">Reset Camera</button>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="display: flex; justify-content: center; align-items: center; flex-direction: column;">
                        <div id="modalCanvasContainer" style="width: 100%; height: 100%;"></div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modalElement);

        // Afficher la modale
        const modal = new Modal(modalElement);
        modal.show();

        modalElement.addEventListener('shown.bs.modal', () => {
            const modalCanvasContainer = document.getElementById('modalCanvasContainer');
            this.updateCanvasSize(modalCanvasContainer);
            this.renderer.setSize(this.canvasWidth, this.canvasHeight);
            modalCanvasContainer.appendChild(this.renderer.domElement);
            this.camera.aspect = this.canvasWidth / this.canvasHeight;
            this.camera.updateProjectionMatrix();
        });

        modalElement.addEventListener('hidden.bs.modal', () => {
            this.updateCanvasSize(this.canvasContainerTarget);
            this.renderer.setSize(this.canvasWidth, this.canvasHeight);
            this.canvasContainerTarget.appendChild(this.renderer.domElement);
            this.camera.aspect = this.canvasWidth / this.canvasHeight;
            this.camera.updateProjectionMatrix();
            modalElement.remove();
        });
    }
}
