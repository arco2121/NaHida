const applyAppearance = () => {
    const variant    = parseInt(document.getElementById('range_variant').value);
    const pot        = parseInt(document.getElementById('range_pot').value);
    const plantColor = parseInt(document.getElementById('range_plant_color').value);
    const flower     = parseInt(document.getElementById('range_flower').value);
    PlantViewer.setAppearance({ pot_color: pot, plant_variant: variant, plant_color: plantColor, flower_color: flower });
    document.getElementById('modal_edit_plant').close();
};

window.applyAppearance = applyAppearance;
