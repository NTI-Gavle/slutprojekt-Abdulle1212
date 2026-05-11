/**
 * clock.js - Digital clock drawn on canvas
 */

// Wait until page has fully loaded
document.addEventListener('DOMContentLoaded', function () {
    // Get canvas element
    const canvas = document.getElementById('clockCanvas');
    
    // Exit if canvas doesn't exist on page
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    // Canvas size
    canvas.width = 260;
    canvas.height = 80;

    /**
     * Draw clock on canvas
     * Clears canvas and draws background with time text
     */
    function drawClock() {
        const now = new Date();

        // Get hours, minutes and seconds
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeString = hours + ':' + minutes + ':' + seconds;

        // Clear entire canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw background with rounded corners
        ctx.beginPath();
        ctx.roundRect(0, 0, canvas.width, canvas.height, 10);
        ctx.fillStyle = '#1a1a2e';
        ctx.fill();

        // Draw time text
        ctx.font = 'bold 36px "Courier New", monospace';
        ctx.fillStyle = '#00d4ff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(timeString, canvas.width / 2, canvas.height / 2 - 5);

        // Draw small label under time
        ctx.font = '11px Arial, sans-serif';
        ctx.fillStyle = '#8892b0';
        ctx.fillText('Kvitter Clock', canvas.width / 2, canvas.height / 2 + 22);
    }

    // Draw clock immediately and then every second
    drawClock();
    setInterval(drawClock, 1000);
});
