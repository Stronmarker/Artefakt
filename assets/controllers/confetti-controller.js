import { Controller } from '@hotwired/stimulus';
import confetti from 'canvas-confetti';

export default class extends Controller {
    connect() {
        console.log("Confetti lancé");
        this.launchConfetti();
    }

    launchConfetti() {
        const duration = 1 * 1000;
        const animationEnd = Date.now() + duration;
        const defaults = { startVelocity: 30, spread: 360, ticks: 300, zIndex: 0, useWorker: true, disableForReducedMotion: true, scalar: 2,  };

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        const canvas = document.getElementById('confetti-canvas');
        const myConfetti = confetti.create(canvas, { resize: true, useWorker: true });

        const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            const particleCount = 50 * (timeLeft / duration);
            myConfetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            myConfetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            myConfetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.5, 0.5), y: Math.random() - 0.2 } }));
        }, 250);
    }
}
