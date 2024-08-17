import { Controller } from "@hotwired/stimulus";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls";

export default class extends Controller {
    static targets = ["canvasContainer", "resetButton", "pauseButton"];

    connect() {
        console.log("Le contrôleur Stimulus est connecté !");
        
        
        this.initScene();
        
       
        this.createTexturedBackground();
        
        
        this.loadTextures().then(() => {
            this.addCardToScene();
        });

        
        this.initControls();
        
        
        window.addEventListener('resize', () => this.onWindowResize(this.canvasContainerTarget));

        
        this.animate();
    }

    initScene() {
        this.scene = new THREE.Scene();

        this.updateCanvasSize(this.canvasContainerTarget);
        this.camera = new THREE.PerspectiveCamera(75, this.canvasWidth / this.canvasHeight, 0.1, 1000);
        this.camera.position.set(3.5, 2, 0);
        console.log(this.camera.position)

        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(this.canvasWidth, this.canvasHeight);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.canvasContainerTarget.appendChild(this.renderer.domElement);

        
        const ambientLight = new THREE.AmbientLight(0x404040, 50);
        this.scene.add(ambientLight);
    }

    createTexturedBackground() {
        const textureLoader = new THREE.TextureLoader();
        const texture = textureLoader.load('/build/images/backgroundd.41ebbb61.png', () => {
        });
       
        const geometry = new THREE.SphereGeometry(500, 60, 40);
        const material = new THREE.MeshPhysicalMaterial({
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
            const whiteMaterial = new THREE.MeshPhysicalMaterial({ color: 0xffffff });
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
            this.card.position.y = 0.5;
            this.card.position.x = 0;
            this.scene.add(this.card);
        }
    }

    initControls() {
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.minPolarAngle = Math.PI / 6;
        this.controls.maxPolarAngle = Math.PI / 2;
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.1;

        
        this.autoRotate = false;
    }

    animate() {
        requestAnimationFrame(this.animate.bind(this));

        
        if (this.autoRotate && this.card)  {

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
        this.camera.position.set(3.5, 2, 0);
        this.camera.lookAt(0, 1, 0);
        this.controls.update();
    }
}
