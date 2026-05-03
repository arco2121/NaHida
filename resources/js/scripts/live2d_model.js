// ============================================================
//  NaHida Plant Live2D Viewer
//  Sistema di stato versatile per dashboard IoT
// ============================================================

const PlantViewer = (() => {

    const MODEL_PATH = '/live2d/models/NaHida Plant Model/NaHida Plant Model.model3.json';

    const PARAMS = {
        POT_COLOR:       'Pot_Color',
        PLANT_VARIANT:   'PlantVariant',
        FLOWER_COLOR:    'FlowerColor',
        SAD_PLANT:       'SadPlant',
        SAD_PLANT_COLOR: 'SadPlantColor',
        EYE_OPEN_R:      'EyeOpenR',
        EYE_OPEN_L:      'EyeOpenL',
        CLOSED_EYES:     'ClosedEyes',
    };

    const HEALTH_THRESHOLDS = {
        soil_humidity_min:  30,
        air_humidity_min:   40,
        temperature_min:    15,
        temperature_max:    35,
        overdue_multiplier: 1.2,
    };

    let _model          = null;
    let _app            = null;
    let _MotionPriority = null;
    let _isTapping      = false;

    let _currentEyeBlink = 1;
    let _targetEyeBlink  = 1;
    let _nextBlinkTime   = 0;

    let _state = {
        sleeping: false,
        sad: false,
        appearance: { pot_color: 0, plant_variant: 0, flower_color: 0 },
        health: { sad_plant: 0, sad_plant_color: 0 },
    };

    // ----------------------------------------------------------
    //  INIT
    // ----------------------------------------------------------

    async function init(canvasId = 'live2d-canvas') {
        const Live2DModel = PIXI.live2d?.Live2DModel;
        _MotionPriority   = PIXI.live2d?.MotionPriority;

        if (!Live2DModel) {
            console.error(`[PlantViewer] PIXI.live2d non disponibile. Controlla i tag script.`);
            return;
        }

        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.error(`[PlantViewer] Canvas #${canvasId} non trovato.`);
            return;
        }

        const container = canvas.parentElement;
        const W = container.clientWidth || 400;
        const H = container.clientHeight || 500;

        _app = new PIXI.Application({
            view: canvas, width: W, height: H,
            transparent: true, antialias: true,
            resolution: window.devicePixelRatio || 1, autoDensity: true,
        });

        try {
            _model = await Live2DModel.from(MODEL_PATH);
            _app.stage.addChild(_model);
            _fitModel(W, H);

            const motionManager = _model.internalModel.motionManager;
            const originalStartMotion = motionManager.startMotion;

            motionManager.startMotion = function(group, index, priority) {
                if (priority === _MotionPriority.IDLE) {
                    group = _state.sleeping ? 'Sleep' : 'Idle';
                }
                return originalStartMotion.call(this, group, index, priority);
            };

            _model.motion('Idle', 0, _MotionPriority.FORCE);
            _updateExpression();

            canvas.addEventListener('pointerdown', () => tap());

            let _rt;
            window.addEventListener('resize', () => {
                clearTimeout(_rt);
                _rt = setTimeout(() => {
                    const nW = container.clientWidth || 400;
                    const nH = container.clientHeight || 500;
                    _app.renderer.resize(nW, nH);
                    _fitModel(nW, nH);
                }, 100);
            });

            _app.ticker.add(_tickParams);

        } catch (err) {
            console.error(`[PlantViewer] Errore caricamento modello: ${err.message}`);
        }
    }

    // ----------------------------------------------------------
    //  API PUBBLICA
    // ----------------------------------------------------------

    function setAppearance({ pot_color = 0, plant_variant = 0, flower_color = 0 } = {}) {
        _state.appearance = { pot_color, plant_variant, flower_color };
    }

    function setHealth({ soil_humidity, air_humidity, temperature, last_watered, water_interval_hours } = {}) {
        const t = HEALTH_THRESHOLDS;
        let sadScore = 0;

        if (temperature !== undefined && (temperature < t.temperature_min || temperature > t.temperature_max)) sadScore++;
        if (soil_humidity !== undefined && soil_humidity < t.soil_humidity_min) sadScore++;
        if (air_humidity !== undefined && air_humidity < t.air_humidity_min) sadScore++;

        if (last_watered && water_interval_hours) {
            const elapsed = (Date.now() - new Date(last_watered).getTime()) / 3600000;
            if (elapsed > water_interval_hours * t.overdue_multiplier) sadScore++;
        }

        const isSad = sadScore >= 2;
        const intensity = Math.min(sadScore / 3, 1);

        _state.sad = isSad;
        _state.health.sad_plant = isSad ? intensity : 0;
        _state.health.sad_plant_color = isSad ? intensity : 0;

        _updateExpression();
    }

    function setSleeping(sleeping) {
        if (_state.sleeping === sleeping) return;
        _state.sleeping = sleeping;
        _isTapping = false;

        if (_model) {
            const group = sleeping ? 'Sleep' : 'Idle';
            _model.motion(group, 0, _MotionPriority.FORCE);
        }

        _updateExpression();
    }

    function tap() {
        if (!_model || _isTapping) return;

        const normalStates = [0, 1, 4];
        const sadStates = [2];
        const sleepStates = [3];

        _isTapping = true;
        let index = 0;
        if(_state.sleeping) {
            index = sleepStates[Math.floor(Math.random() * sleepStates.length)];
        } else if(_state.sad) {
            index = sadStates[Math.floor(Math.random() * sadStates.length)];
        } else {
            index = normalStates[Math.floor(Math.random() * normalStates.length)];
        }

        _model.motion('Tap', index, _MotionPriority.FORCE)
            .finally(() => {
                _isTapping = false;
            });
    }

    // ----------------------------------------------------------
    //  LOGICA INTERNA
    // ----------------------------------------------------------

    function _updateExpression() {
        if (!_model) return;
        if (_state.sleeping)  _model.expression('Sleep');
        else if (_state.sad)  _model.expression('Sad');
        else                  _model.expression('Normal');
    }

    function _tickBlink() {
        const now = performance.now();
        if (now > _nextBlinkTime) {
            _targetEyeBlink = 0;

            setTimeout(() => { _targetEyeBlink = 1; }, 100);
            _nextBlinkTime = now + 2000 + Math.random() * 4000;
        }
    }

    function _tickParams() {
        if (!_model) return;
        const core = _model.internalModel.coreModel;
        const ids  = core._parameterIds;
        const vals = core._parameterValues;
        if (!ids || !ids.length) return;

        _setParam(ids, vals, PARAMS.POT_COLOR,       _state.appearance.pot_color);
        _setParam(ids, vals, PARAMS.PLANT_VARIANT,    _state.appearance.plant_variant);
        _setParam(ids, vals, PARAMS.FLOWER_COLOR,     _state.appearance.flower_color);
        _setParam(ids, vals, PARAMS.SAD_PLANT,        _state.health.sad_plant);
        _setParam(ids, vals, PARAMS.SAD_PLANT_COLOR,  _state.health.sad_plant_color);

        if (!_state.sleeping && !_isTapping) {
            _tickBlink();
            _currentEyeBlink += (_targetEyeBlink - _currentEyeBlink) * 0.35;

            _setParam(ids, vals, PARAMS.EYE_OPEN_L, _currentEyeBlink);
            _setParam(ids, vals, PARAMS.EYE_OPEN_R, _currentEyeBlink);
        }

        if (!_isTapping) {
            _setParam(ids, vals, PARAMS.CLOSED_EYES, _state.sleeping ? 0 : 1);
        }
    }

    function _setParam(ids, vals, paramId, value) {
        const idx = ids.indexOf(paramId);
        if (idx !== -1) vals[idx] = value;
    }

    function _fitModel(W, H) {
        if (!_model) return;
        const scale = Math.min(
            W / _model.internalModel.originalWidth,
            H / _model.internalModel.originalHeight
        ) * 0.85;
        _model.scale.set(scale);
        _model.x = W / 2;
        _model.y = H / 2;
        _model.anchor.set(0.5, 0.5);
    }

    return { init, setAppearance, setHealth, setSleeping, tap };

})();

window.addEventListener('DOMContentLoaded', () => PlantViewer.init());
