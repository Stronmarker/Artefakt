import { Controller } from "@hotwired/stimulus";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls";
import { Modal } from "bootstrap";

export default class extends Controller {
    static targets = ["canvasContainer", "resetButton", "pauseButton"];

    connect() {
        console.log("Le contrôleur Stimulus est connecté !");
        
        // Initialisation de la scène
        this.initScene();
        
        // Création du fond texturé
        this.createTexturedBackground();
        
        // Chargement des textures et ajout de la carte
        this.loadTextures().then(() => {
            this.addCardToScene();
        });

        // Initialisation des contrôles pour bouger la carte
        this.initControls();
        
        // Ajuster la taille de la fenêtre
        window.addEventListener('resize', () => this.onWindowResize(this.canvasContainerTarget));

        // Fonction d'animation
        this.animate();
    }

    initScene() {
        this.scene = new THREE.Scene();

        this.updateCanvasSize(this.canvasContainerTarget);
        this.camera = new THREE.PerspectiveCamera(75, this.canvasWidth / this.canvasHeight, 0.1, 1000);
        this.camera.position.set(2, 2, 4);

        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(this.canvasWidth, this.canvasHeight);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.canvasContainerTarget.appendChild(this.renderer.domElement);

        // Ajout de lumières
        const ambientLight = new THREE.AmbientLight(0x404040, 50);
        this.scene.add(ambientLight);

        // Désactiver ou réduire l'intensité de la lumière directionnelle
        // const directionalLight = new THREE.DirectionalLight(0xffffff, 0.5);
        // directionalLight.position.set(5, 10, 7.5);
        // this.scene.add(directionalLight);
    }

    createTexturedBackground() {
        const textureLoader = new THREE.TextureLoader();
        const texture = textureLoader.load('/build/images/backgroundd.41ebbb61.png', () => {
            texture.wrapS = THREE.RepeatWrapping;
            texture.wrapT = THREE.RepeatWrapping;
            texture.repeat.set(1, 1);
        });
        const cubeTextureLoader = new THREE.CubeTextureLoader()

        const envMap = cubeTextureLoader.load('/build/images/lonely_road_afternoon_puresky_2k.9a363b18')
        this.scene.environment = envMap; 

        const geometry = new THREE.SphereGeometry(500, 60, 40);
        const material = new THREE.MeshBasicMaterial({
            map: texture,
            side: THREE.BackSide
        });

        const sphere = new THREE.Mesh(geometry, material);
        this.scene.add(sphere);
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
            const whiteMaterial = new THREE.MeshBasicMaterial({ color: 0xffffff });
            const cardMaterials = [
                new THREE.MeshPhysicalMaterial({ map: this.frontTexture }),
                new THREE.MeshPhysicalMaterial({ map: this.towardTexture }),
                whiteMaterial,
                whiteMaterial,
                whiteMaterial,
                whiteMaterial
            ];
            const cardGeometry = new THREE.BoxGeometry(0.010, 2, 3.05);
            this.card = new THREE.Mesh(cardGeometry, cardMaterials);
            this.card.position.y = 1.5;
            this.scene.add(this.card);
        }
    }

    initControls() {
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.minPolarAngle = Math.PI / 6;
        this.controls.maxPolarAngle = Math.PI / 2;
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.1;

        // Variables pour gérer la rotation automatique
        this.autoRotate = false;
    }

    animate() {
        requestAnimationFrame(this.animate.bind(this));

        // Rotation automatique de la carte si autoRotate est true
        if (this.autoRotate && this.card)  {
            // console.log(this.card.rotation.y)

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
        
    }

    resetCamera() {
        this.camera.position.set(0, 2, 10);
        this.camera.lookAt(0, 1, 0);
        this.controls.update();
    }

    // showModalCanvas(event) {
    //     // Création de la modale
    //     const modalElement = document.createElement('div');
    //     modalElement.classList.add('modal', 'fade');
    //     modalElement.id = 'canvasModal';
    //     modalElement.setAttribute('tabindex', '-1');
    //     modalElement.setAttribute('aria-labelledby', 'canvasModalLabel');
    //     modalElement.setAttribute('aria-hidden', 'true');

    //     modalElement.innerHTML = `
    //         <div class="modal-dialog modal-fullscreen">
    //             <div class="modal-content">
    //                 <div class="modal-header">
    //                     <div>
    //                         <button class="btn btn-secondary me-2" data-action="click->image#toggleRotation">Pause Rotation</button>
    //                         <button class="btn btn-secondary me-2" data-action="click->image#resetCamera">Reset Camera</button>
    //                     </div>
    //                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    //                 </div>
    //                 <div class="modal-body" style="display: flex; justify-content: center; align-items: center; flex-direction: column;">
    //                     <div id="modalCanvasContainer" style="width: 100%; height: 100%;"></div>
    //                 </div>
    //             </div>
    //         </div>
    //     `;

    //     document.body.appendChild(modalElement);

    //     // Afficher la modale
    //     const modal = new Modal(modalElement);
    //     modal.show();

    //     modalElement.addEventListener('shown.bs.modal', () => {
    //         const modalCanvasContainer = document.getElementById('modalCanvasContainer');
    //         this.updateCanvasSize(modalCanvasContainer);
    //         this.renderer.setSize(this.canvasWidth, this.canvasHeight);
    //         modalCanvasContainer.appendChild(this.renderer.domElement);
    //         this.camera.aspect = this.canvasWidth / this.canvasHeight;
    //         this.camera.updateProjectionMatrix();
    //     });

    //     modalElement.addEventListener('hidden.bs.modal', () => {
    //         this.updateCanvasSize(this.canvasContainerTarget);
    //         this.renderer.setSize(this.canvasWidth, this.canvasHeight);
    //         this.canvasContainerTarget.appendChild(this.renderer.domElement);
    //         this.camera.aspect = this.canvasWidth / this.canvasHeight;
    //         this.camera.updateProjectionMatrix();
    //         modalElement.remove();
    //     });
    // }
}
