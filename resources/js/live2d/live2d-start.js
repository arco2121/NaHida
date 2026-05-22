import { PlantViewer } from "./live2d-viewer.js";

window.addEventListener('DOMContentLoaded', async () => {
    await PlantViewer.init('live2d-canvas');

    if (window.PLANT_APPEARANCE)
        PlantViewer.setAppearance(window.PLANT_APPEARANCE);
    else
        PlantViewer.randomizeAppearance();
});
