import { PlantViewer } from "../live2d/live2d-viewer.js";

const TEMPLATES = {
    tropicale:    { hum_min: 60, hum_max: 90, temp_min: 20, temp_max: 35, soil_min: 50, soil_max: 80 },
    mediterraneo: { hum_min: 40, hum_max: 70, temp_min: 15, temp_max: 28, soil_min: 35, soil_max: 65 },
    succulente:   { hum_min: 10, hum_max: 40, temp_min: 15, temp_max: 38, soil_min: 10, soil_max: 35 },
    custom:       null,
};

let _unit = 'ore';

function updateModel() {
    PlantViewer.setAppearance({
        plant_variant: parseInt(document.getElementById('range_variant').value),
        plant_color:   parseInt(document.getElementById('range_plant_color').value),
        flower_color:  parseInt(document.getElementById('range_flower').value),
        pot_color:     parseInt(document.getElementById('range_pot').value),
    });
}

function selectTemplate(btn) {
    const key = btn.dataset.template;

    document.querySelectorAll('.template-btn').forEach(b => {
        b.classList.remove('border-primary', 'bg-primary/10');
        b.classList.add('border-base-200', 'bg-base-200');
    });
    btn.classList.add('border-primary', 'bg-primary/10');
    btn.classList.remove('border-base-200', 'bg-base-200');

    document.getElementById('summary_template').textContent = btn.querySelector('p.font-bold').textContent;

    const tpl = TEMPLATES[key];
    const fields = ['hum_min', 'hum_max', 'temp_min', 'temp_max', 'soil_min', 'soil_max'];

    if (tpl) {
        fields.forEach(id => {
            const el = document.getElementById(id);
            el.value    = tpl[id];
            el.readOnly = true;
            el.classList.add('opacity-60');
        });
    } else {
        fields.forEach(id => {
            const el = document.getElementById(id);
            el.readOnly = false;
            el.classList.remove('opacity-60');
        });
    }
}

function setUnit(unit) {
    _unit = unit;

    document.getElementById('btn_ore').classList.toggle('bg-primary',          unit === 'ore');
    document.getElementById('btn_ore').classList.toggle('text-primary-content', unit === 'ore');
    document.getElementById('btn_ore').classList.toggle('bg-base-200',          unit !== 'ore');
    document.getElementById('btn_ore').classList.toggle('text-base-content',    unit !== 'ore');

    document.getElementById('btn_giorni').classList.toggle('bg-primary',          unit === 'giorni');
    document.getElementById('btn_giorni').classList.toggle('text-primary-content', unit === 'giorni');
    document.getElementById('btn_giorni').classList.toggle('bg-base-200',          unit !== 'giorni');
    document.getElementById('btn_giorni').classList.toggle('text-base-content',    unit !== 'giorni');

    updateWaterPreview();
}

function updateWaterPreview() {
    const val = document.getElementById('water_interval').value;
    const txt = val ? `Verrà annaffiata ogni ${val} ${_unit}` : `Verrà annaffiata ogni — ${_unit}`;
    document.getElementById('water_preview').textContent = txt;

    const summary = document.getElementById('summary_water');
    if (summary) summary.textContent = val ? `ogni ${val} ${_unit}` : '—';
}

function openConfirmModal() {
    const name = document.getElementById('plant_name').value || '—';
    const summaryName = document.getElementById('summary_name');
    if (summaryName) summaryName.textContent = name;
    updateWaterPreview();
    document.getElementById('modal_confirm').showModal();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.template-btn').forEach(btn => {
        btn.addEventListener('click', () => selectTemplate(btn));
    });

    document.querySelectorAll('.unit-btn').forEach(btn => {
        btn.addEventListener('click', () => setUnit(btn.dataset.unit));
    });

    document.getElementById('water_interval').addEventListener('input', updateWaterPreview);

    document.getElementById('btn_confirm').addEventListener('click', openConfirmModal);

    const ranges = [
        { range: 'range_variant',    label: 'lbl_variant' },
        { range: 'range_plant_color', label: 'lbl_plant_color' },
        { range: 'range_flower',     label: 'lbl_flower' },
        { range: 'range_pot',        label: 'lbl_pot' },
    ];

    ranges.forEach(({ range, label }) => {
        document.getElementById(range).addEventListener('input', function () {
            document.getElementById(label).textContent = this.value;
            updateModel();
        });
    });
});
