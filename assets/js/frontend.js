(function($) {
    'use strict';

    class HoverSyncEngine {
        constructor() {
            this.config = window.ehepConfig || {};
            this.init();
        }

        init() {
            // Wait for Elementor to be ready or just document ready
            const start = () => {
                this.scan();
                this.bindGlobal();
            };

            if (window.elementorFrontend) {
                $(window).on('elementor/frontend/init', start);
            } else {
                $(start);
            }
        }

        scan() {
            const elements = document.querySelectorAll('[data-hoversync]');
            elements.forEach(el => {
                try {
                    const data = JSON.parse(el.getAttribute('data-hoversync'));
                    if (!data) return;

                    this.setupElement(el, data);
                } catch (e) {
                    console.error('[HoverSync] Parse Error:', e);
                }
            });
        }

        setupElement(el, data) {
            // Prevent duplicate bindings
            if (el.dataset.hoversyncBound) return;
            el.dataset.hoversyncBound = 'true';

            // Only bind events if it's a source or hybrid
            if (data.role === 'source' || data.role === 'both') {
                el.addEventListener('mouseenter', () => this.handleHover(data, true));
                el.addEventListener('mouseleave', () => this.handleHover(data, false));
                
                // Add indicator for editor/debug
                if (this.config.isEditor) {
                    el.style.outline = '1px dashed rgba(124, 58, 237, 0.3)';
                }
            }
        }

        handleHover(data, active) {
            if (!data.fx || !Array.isArray(data.fx)) return;

            // Group effects by selector to combine transforms and filters
            const selectorGroups = {};
            data.fx.forEach(fx => {
                if (!selectorGroups[fx.selector]) selectorGroups[fx.selector] = [];
                selectorGroups[fx.selector].push(fx);
            });

            Object.keys(selectorGroups).forEach(selector => {
                const targets = document.querySelectorAll(selector);
                const effects = selectorGroups[selector];
                if (!targets.length) return;

                targets.forEach(target => {
                    if (active) {
                        this.applyEffects(target, effects);
                    } else {
                        this.resetEffects(target, effects);
                    }
                });
            });
        }

        applyEffects(el, effects) {
            // Backup original inline styles for transitions and properties if not already done
            if (el.dataset.hoversyncOriginalTransition === undefined) {
                el.dataset.hoversyncOriginalTransition = el.style.transition || '';
            }

            let transforms = [];
            let filters = [];
            let transitions = [];

            effects.forEach(fx => {
                const duration = fx.dur !== undefined ? fx.dur : 300;
                const easing = fx.eas || 'ease';

                switch(fx.type) {
                    case 'scale':
                        transforms.push(`scale(${fx.val})`);
                        transitions.push(`transform ${duration}ms ${easing}`);
                        break;
                    case 'rotate':
                        transforms.push(`rotate(${fx.val})`);
                        transitions.push(`transform ${duration}ms ${easing}`);
                        break;
                    case 'skewX':
                        transforms.push(`skewX(${fx.val})`);
                        transitions.push(`transform ${duration}ms ${easing}`);
                        break;
                    case 'skewY':
                        transforms.push(`skewY(${fx.val})`);
                        transitions.push(`transform ${duration}ms ${easing}`);
                        break;
                    case 'translateX':
                        transforms.push(`translateX(${fx.val})`);
                        transitions.push(`transform ${duration}ms ${easing}`);
                        break;
                    case 'translateY':
                        transforms.push(`translateY(${fx.val})`);
                        transitions.push(`transform ${duration}ms ${easing}`);
                        break;
                    case 'opacity':
                        el.style.opacity = fx.val;
                        transitions.push(`opacity ${duration}ms ${easing}`);
                        break;
                    case 'blur':
                        filters.push(`blur(${fx.val})`);
                        transitions.push(`filter ${duration}ms ${easing}`);
                        break;
                    case 'grayscale':
                        filters.push(`grayscale(${fx.val})`);
                        transitions.push(`filter ${duration}ms ${easing}`);
                        break;
                    case 'brightness':
                        filters.push(`brightness(${fx.val})`);
                        transitions.push(`filter ${duration}ms ${easing}`);
                        break;
                    case 'saturate':
                        filters.push(`saturate(${fx.val})`);
                        transitions.push(`filter ${duration}ms ${easing}`);
                        break;
                    case 'hue-rotate':
                        filters.push(`hue-rotate(${fx.val})`);
                        transitions.push(`filter ${duration}ms ${easing}`);
                        break;
                    case 'background':
                        el.style.backgroundColor = fx.val;
                        transitions.push(`background-color ${duration}ms ${easing}`);
                        break;
                    case 'custom':
                        this.applyCustomStyles(el, fx.val, duration, easing, transitions);
                        break;
                }
            });

            if (transitions.length) {
                el.style.transition = transitions.join(', ');
            }
            if (transforms.length) el.style.transform = transforms.join(' ');
            if (filters.length) el.style.filter = filters.join(' ');
        }

        resetEffects(el, effects) {
            effects.forEach(fx => {
                switch(fx.type) {
                    case 'scale':
                    case 'rotate':
                    case 'skewX':
                    case 'skewY':
                    case 'translateX':
                    case 'translateY':
                        el.style.transform = '';
                        break;
                    case 'opacity':
                        el.style.opacity = '';
                        break;
                    case 'blur':
                    case 'grayscale':
                    case 'brightness':
                    case 'saturate':
                    case 'hue-rotate':
                        el.style.filter = '';
                        break;
                    case 'background':
                        el.style.backgroundColor = '';
                        break;
                    case 'custom':
                        this.resetCustomStyles(el, fx.val);
                        break;
                }
            });

            // Restore original transition
            if (el.dataset.hoversyncOriginalTransition !== undefined) {
                el.style.transition = el.dataset.hoversyncOriginalTransition;
            } else {
                el.style.transition = '';
            }
        }

        applyCustomStyles(el, cssString, duration, easing, transitions) {
            if (!cssString) return;
            const pairs = cssString.split(';');
            pairs.forEach(pair => {
                const [prop, val] = pair.split(':');
                if (prop && val) {
                    const cleanProp = prop.trim();
                    const camelProp = cleanProp.replace(/-([a-z])/g, (g) => g[1].toUpperCase());
                    el.style[camelProp] = val.trim();
                    transitions.push(`${cleanProp} ${duration}ms ${easing}`);
                }
            });
        }

        resetCustomStyles(el, cssString) {
            if (!cssString) return;
            const pairs = cssString.split(';');
            pairs.forEach(pair => {
                const [prop] = pair.split(':');
                if (prop) {
                    const camelProp = prop.trim().replace(/-([a-z])/g, (g) => g[1].toUpperCase());
                    el.style[camelProp] = '';
                }
            });
        }

        bindGlobal() {
            // Listen for Elementor section/widget re-renders in editor
            if (window.elementorFrontend && elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/global', () => {
                    this.scan();
                });
            }
        }
    }

    // Launch
    window.HoverSync = new HoverSyncEngine();

})(jQuery);