import { PlantViewer } from "./live2d-viewer.js";

window.addEventListener('DOMContentLoaded', async () => {
    await PlantViewer.init('live2d-canvas');
    PlantViewer.randomizeAppearance();
});
