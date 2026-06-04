import { PlantViewer } from "../live2d/live2d-viewer.js";

const TEMPLATES = {
    tropicale:    { hum_min: 60, hum_max: 90, temp_min: 20, temp_max: 35, soil_hum_min: 50, soil_hum_max: 80 },
    mediterraneo: { hum_min: 40, hum_max: 70, temp_min: 15, temp_max: 28, soil_hum_min: 35, soil_hum_max: 65 },
    succulente:   { hum_min: 10, hum_max: 40, temp_min: 15, temp_max: 38, soil_hum_min: 10, soil_hum_max: 35 },
    basilico: { hum_min: 50, hum_max: 70, temp_min: 20, temp_max: 30, soil_hum_min: 60, soil_hum_max: 80 },
    monstera: { hum_min: 60, hum_max: 80, temp_min: 18, temp_max: 30, soil_hum_min: 50, soil_hum_max: 70 },
    aloe:     { hum_min: 20, hum_max: 40, temp_min: 15, temp_max: 30, soil_hum_min: 10, soil_hum_max: 30 },
    lavanda: { hum_min: 30, hum_max: 50, temp_min: 15, temp_max: 30, soil_hum_min: 20, soil_hum_max: 40 },
    custom:       null,
};

let _unit = 'ore';

// -------------------------------------------------------
//  Modello Live2D
// -------------------------------------------------------
function updateModel() {
    PlantViewer.setAppearance({
        plant_variant: parseInt(document.getElementById('range_variant').value),
        plant_color:   parseInt(document.getElementById('range_plant_color').value),
        flower_color:  parseInt(document.getElementById('range_flower').value),
        pot_color:     parseInt(document.getElementById('range_pot').value),
    });
}

// -------------------------------------------------------
//  Template condizioni
// -------------------------------------------------------
function selectTemplate(btn) {
    const key = btn.dataset.template;

    document.querySelectorAll('.template-btn').forEach(b => {
        b.classList.remove('border-primary', 'bg-primary/10');
        b.classList.add('border-base-200', 'bg-base-200');
    });
    btn.classList.add('border-primary', 'bg-primary/10');
    btn.classList.remove('border-base-200', 'bg-base-200');

    document.getElementById('summary_template').textContent =
        btn.querySelector('p.font-bold').textContent;

    const tpl = TEMPLATES[key];
    // I field ID ora corrispondono ai name: soil_hum_min / soil_hum_max
    const fields = ['hum_min', 'hum_max', 'temp_min', 'temp_max', 'soil_hum_min', 'soil_hum_max'];

    if (tpl) {
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.value    = tpl[id];
            el.readOnly = true;
            el.classList.add('opacity-60');
        });
    } else {
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.readOnly = false;
            el.classList.remove('opacity-60');
        });
    }
}

// -------------------------------------------------------
//  Unità di misura annaffiatura
// -------------------------------------------------------
function setUnit(unit) {
    _unit = unit;

    document.getElementById('btn_ore').classList.toggle('bg-primary',           unit === 'ore');
    document.getElementById('btn_ore').classList.toggle('text-primary-content',  unit === 'ore');
    document.getElementById('btn_ore').classList.toggle('bg-base-200',           unit !== 'ore');
    document.getElementById('btn_ore').classList.toggle('text-base-content',     unit !== 'ore');

    document.getElementById('btn_giorni').classList.toggle('bg-primary',           unit === 'giorni');
    document.getElementById('btn_giorni').classList.toggle('text-primary-content',  unit === 'giorni');
    document.getElementById('btn_giorni').classList.toggle('bg-base-200',           unit !== 'giorni');
    document.getElementById('btn_giorni').classList.toggle('text-base-content',     unit !== 'giorni');

    updateWaterPreview();
}

function updateWaterPreview() {
    const val = document.getElementById('water_interval').value;
    const txt = val
        ? `Verrà annaffiata ogni ${val} ${_unit}`
        : `Verrà annaffiata ogni — ${_unit}`;
    document.getElementById('water_preview').textContent = txt;

    const summary = document.getElementById('summary_water');
    if (summary) summary.textContent = val ? `ogni ${val} ${_unit}` : '—';
}

// -------------------------------------------------------
//  Modal di conferma
// -------------------------------------------------------
function openConfirmModal() {
    const name = document.getElementById('plant_name').value || '—';
    const summaryName = document.getElementById('summary_name');
    if (summaryName) summaryName.textContent = name;
    updateWaterPreview();
    document.getElementById('modal_confirm').showModal();
}

// -------------------------------------------------------
//  Invio del form
//  Calcola watering_cycle in ore e poi fa submit
// -------------------------------------------------------
function submitPlantForm() {
    const val = parseInt(document.getElementById('water_interval').value) || 0;
    const multiplier = _unit === 'giorni' ? 24 : 1;
    document.getElementById('watering_cycle_hidden').value = val * multiplier;

    document.getElementById('plant-form').submit();
}

// -------------------------------------------------------
//  Init
// -------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {

    // Template buttons
    document.querySelectorAll('.template-btn').forEach(btn => {
        btn.addEventListener('click', () => selectTemplate(btn));
    });

    // Unit buttons
    document.querySelectorAll('.unit-btn').forEach(btn => {
        btn.addEventListener('click', () => setUnit(btn.dataset.unit));
    });

    // Water interval preview
    document.getElementById('water_interval').addEventListener('input', updateWaterPreview);

    // "Aggiungi pianta" → apre il modal di conferma
    document.getElementById('btn_confirm').addEventListener('click', openConfirmModal);

    // Slider appearance → aggiorna label + modello
    const ranges = [
        { range: 'range_variant',     label: 'lbl_variant' },
        { range: 'range_plant_color', label: 'lbl_plant_color' },
        { range: 'range_flower',      label: 'lbl_flower' },
        { range: 'range_pot',         label: 'lbl_pot' },
    ];
    ranges.forEach(({ range, label }) => {
        document.getElementById(range).addEventListener('input', function () {
            document.getElementById(label).textContent = this.value;
            updateModel();
        });
    });

    // ✅ Bottone "Conferma" nel modal → calcola watering_cycle e invia il form
    const modalConfirmBtn = document.getElementById('btn_submit_plant');
    if (modalConfirmBtn) {
        modalConfirmBtn.addEventListener('click', (e) => {
            e.preventDefault();
            submitPlantForm();
        });
    }
});
