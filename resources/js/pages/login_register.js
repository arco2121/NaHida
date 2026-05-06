import { PlantViewer } from "../live2d/live2d-viewer.js";
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("passwordInput").addEventListener("blur", () => PlantViewer.setPasswordMode(false));
    document.getElementById("passwordInput").addEventListener("focus", () => PlantViewer.setPasswordMode(true));
});
