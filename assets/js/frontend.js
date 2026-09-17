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
            // Check if we should skip in editor
            const isEditMode = window.elementorFrontend && elementorFrontend.isEditMode();
            if (isEditMode && data.editorPreview === false) {
                return;
            }

            // Prevent double binding - reset if needed for editor
            if (el.getAttribute('data-hoversync-init')) {
                if (isEditMode) {
                    this.cleanupElement(el);
                } else {
                    return;
                }
            }
            el.setAttribute('data-hoversync-init', '1');

            // --- Trigger Mode Logic ---
            if (data.event === 'scroll' && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                this.setupScrollTrigger(el, data);
            } else if (data.role === 'source' || data.role === 'both') {
                if (data.event === 'click') {
                    let activeState = false;
                    const clickHandler = (e) => {
                        e.preventDefault();
                        activeState = !activeState;
                        this.handleInteraction(data, activeState);
                    };
                    el.addEventListener('click', clickHandler);
                    el._hoversyncClick = clickHandler;
                } else {
                    const enterHandler = () => this.handleInteraction(data, true);
                    const leaveHandler = () => this.handleInteraction(data, false);
                    el.addEventListener('mouseenter', enterHandler);
                    el.addEventListener('mouseleave', leaveHandler);
                    el._hoversyncEnter = enterHandler;
                    el._hoversyncLeave = leaveHandler;
                }
                
                // Track mouse for Parallax
                const hasParallax = data.fx && data.fx.some(fx => fx.type === 'parallax');
                if (hasParallax) {
                    const parallaxHandler = (e) => this.handleParallax(e, el, data);
                    el.addEventListener('mousemove', parallaxHandler);
                    el._hoversyncParallax = parallaxHandler;
                }

                if (isEditMode) {
                    el.style.outline = '1px dashed rgba(124, 58, 237, 0.4)';
                }
            }
        }

        cleanupElement(el) {
            if (el._hoversyncEnter) el.removeEventListener('mouseenter', el._hoversyncEnter);
            if (el._hoversyncLeave) el.removeEventListener('mouseleave', el._hoversyncLeave);
            if (el._hoversyncClick) el.removeEventListener('click', el._hoversyncClick);
            if (el._hoversyncParallax) el.removeEventListener('mousemove', el._hoversyncParallax);
            
            // Clean GSAP properties
            if (typeof gsap !== 'undefined') {
                gsap.killTweensOf(el);
                // Also kill triggers associated with this element
                ScrollTrigger.getAll().forEach(t => {
                    if (t.vars.trigger === el) t.kill();
                });
            }
        }

        setupScrollTrigger(el, data) {
            if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
            
            gsap.registerPlugin(ScrollTrigger);
            
            const isEditMode = window.elementorFrontend && elementorFrontend.isEditMode();
            let viewportPoint = 50;
            if (typeof data.scrollViewport === 'number') {
                viewportPoint = data.scrollViewport;
            } else if (typeof data.scrollViewport === 'object' && data.scrollViewport !== null) {
                viewportPoint = data.scrollViewport.size ?? 50;
            } else if (typeof data.scrollViewport === 'string') {
                viewportPoint = parseFloat(data.scrollViewport) || 50;
            }

            const selectorGroups = {};
            data.fx.forEach(fx => {
                if (!selectorGroups[fx.selector]) selectorGroups[fx.selector] = [];
                selectorGroups[fx.selector].push(fx);
            });

            Object.keys(selectorGroups).forEach(selector => {
                const targets = document.querySelectorAll(selector);
                const effects = selectorGroups[selector];
                if (!targets.length) return;

                const stagger = data.stagger || 0;
                const ease = effects[0].ease || 'power2.out';
                const duration = (effects[0].dur || 800) / 1000;
                const isScrub = effects.some(fx => fx.scrub);
                const triggerEl = document.body.contains(el) ? el : targets[0];

                const tl = gsap.timeline({
                    scrollTrigger: {
                        trigger: triggerEl,
                        start: `top ${viewportPoint}%`,
                        end: isScrub ? `bottom ${Math.max(0, viewportPoint - 30)}%` : `bottom top`,
                        scrub: isScrub ? 0.5 : false,
                        toggleActions: isScrub ? undefined : "play none none reverse",
                        markers: isEditMode && data.editorPreview !== false,
                        invalidateOnRefresh: true
                    }
                });

                targets.forEach((target, index) => {
                    // Check for text split
                    if (effects.some(fx => fx.type === 'text_reveal')) {
                        this.splitText(target);
                        const chars = target.querySelectorAll('.hsync-char');
                        if (chars.length) {
                             tl.fromTo(chars, 
                                { opacity: 0, y: 20, rotateX: -90 },
                                {
                                    opacity: 1,
                                    y: 0,
                                    rotateX: 0,
                                    stagger: 0.05,
                                    duration: duration,
                                    ease: ease,
                                }, 
                                index * stagger
                             );
                             return;
                        }
                    }

                    const props = this.getGSAPProps(effects, true);
                    const initialProps = this.getGSAPProps(effects, false);
                    
                    tl.fromTo(target, initialProps, {
                        ...props,
                        duration: duration,
                        ease: ease,
                        overwrite: 'auto'
                    }, index * stagger);
                });
            });
            
            setTimeout(() => {
                ScrollTrigger.refresh();
            }, 150);
        }

        splitText(el) {
            if (el.getAttribute('data-hsync-split')) return;
            const text = el.innerText;
            el.innerHTML = text.split('').map(char => `<span class="hsync-char" style="display:inline-block; transform-origin: center;">${char === ' ' ? '&nbsp;' : char}</span>`).join('');
            el.setAttribute('data-hsync-split', '1');
            gsap.set(el, { perspective: 400 });
        }

        handleParallax(e, sourceEl, data) {
            const rect = sourceEl.getBoundingClientRect();
            const relX = (e.clientX - rect.left) / rect.width - 0.5; 
            const relY = (e.clientY - rect.top) / rect.height - 0.5;

            data.fx.forEach(fx => {
                if (fx.type === 'parallax') {
                    const targets = document.querySelectorAll(fx.selector);
                    targets.forEach(target => {
                        const speed = fx.parallax || 5;
                        gsap.to(target, {
                            x: relX * speed * 10,
                            y: relY * speed * 10,
                            duration: 0.4,
                            ease: "power2.out",
                            overwrite: 'auto'
                        });
                    });
                }
                
                if (fx.type === 'tilt') {
                    const targets = document.querySelectorAll(fx.selector);
                    targets.forEach(target => {
                        gsap.to(target, {
                            rotateY: relX * 30, // 30 deg max tilt
                            rotateX: -relY * 30,
                            duration: 0.5,
                            ease: "power2.out",
                            transformPerspective: 1000,
                            overwrite: 'auto'
                        });
                    });
                }
            });
        }

        handleInteraction(data, active) {
            if (!data.fx || !Array.isArray(data.fx)) return;

            const selectorGroups = {};
            data.fx.forEach(fx => {
                if (!selectorGroups[fx.selector]) selectorGroups[fx.selector] = [];
                selectorGroups[fx.selector].push(fx);
            });

            const stagger = data.stagger || 0;

            Object.keys(selectorGroups).forEach(selector => {
                const targets = document.querySelectorAll(selector);
                const effects = selectorGroups[selector];
                if (!targets.length) return;

                targets.forEach((target, index) => {
                    // Handle delay if stagger exists
                    setTimeout(() => {
                        if (active) {
                            this.applyEffects(target, effects);
                        } else {
                            this.resetEffects(target, effects);
                        }
                    }, index * stagger * 1000);
                });
            });
        }

        applyEffects(el, effects) {
            const gsapEffects = effects.filter(fx => fx.engine === 'gsap');
            const cssEffects = effects.filter(fx => fx.engine !== 'gsap');

            // Handle GSAP
            if (gsapEffects.length) {
                this.playGSAP(el, gsapEffects, true);
            }

            // Handle CSS Legacy
            if (cssEffects.length) {
                let transforms = [];
                let filters = [];
                let maxDuration = 0;

                cssEffects.forEach(fx => {
                    maxDuration = Math.max(maxDuration, fx.dur);
                    switch(fx.type) {
                        case 'scale': transforms.push(`scale(${fx.val})`); break;
                        case 'rotate': transforms.push(`rotate(${fx.val})`); break;
                        case 'translateX': transforms.push(`translateX(${fx.val})`); break;
                        case 'translateY': transforms.push(`translateY(${fx.val})`); break;
                        case 'opacity': el.style.opacity = fx.val; break;
                        case 'blur': filters.push(`blur(${fx.val})`); break;
                        case 'grayscale': filters.push(`grayscale(${fx.val})`); break;
                        case 'contrast': filters.push(`contrast(${fx.val})`); break;
                        case 'brightness': filters.push(`brightness(${fx.val})`); break;
                        case 'background': el.style.backgroundColor = fx.val; break;
                        case 'custom': this.applyCustomStyles(el, fx.val); break;
                    }
                });

                el.style.transition = `all ${maxDuration}ms ease`;
                if (transforms.length) el.style.transform = transforms.join(' ');
                if (filters.length) el.style.filter = filters.join(' ');
            }
        }

        resetEffects(el, effects) {
            const gsapEffects = effects.filter(fx => fx.engine === 'gsap');
            const cssEffects = effects.filter(fx => fx.engine !== 'gsap');

            // Reset GSAP
            if (gsapEffects.length) {
                this.playGSAP(el, gsapEffects, false);
            }

            // Reset CSS Legacy
            if (cssEffects.length) {
                let maxDuration = 0;
                cssEffects.forEach(fx => {
                    maxDuration = Math.max(maxDuration, fx.dur);
                    switch(fx.type) {
                        case 'scale':
                        case 'rotate':
                        case 'translateX':
                        case 'translateY':
                            el.style.transform = '';
                            break;
                        case 'opacity':
                            el.style.opacity = '';
                            break;
                        case 'blur':
                        case 'grayscale':
                        case 'contrast':
                        case 'brightness':
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
                el.style.transition = `all ${maxDuration}ms ease`;
            }
        }

        playGSAP(el, effects, active) {
            if (typeof gsap === 'undefined') return;

            const props = this.getGSAPProps(effects, active);
            const maxDuration = Math.max(...effects.map(fx => fx.dur || 300));
            const ease = effects[0].ease || 'power2.out';

            gsap.to(el, {
                ...props,
                duration: maxDuration / 1000,
                ease: ease,
                overwrite: 'auto'
            });
        }

        getGSAPProps(effects, active) {
            let props = {};
            effects.forEach(fx => {
                if (active) {
                    switch(fx.type) {
                        case 'scale': props.scale = fx.val; break;
                        case 'rotate': props.rotate = fx.val; break;
                        case 'translateX': props.x = fx.val; break;
                        case 'translateY': props.y = fx.val; break;
                        case 'opacity': props.opacity = fx.val; break;
                        case 'blur': props.filter = `blur(${fx.val})`; break;
                        case 'grayscale': props.filter = `grayscale(${fx.val})`; break;
                        case 'contrast': props.filter = `contrast(${fx.val})`; break;
                        case 'brightness': props.filter = `brightness(${fx.val})`; break;
                        case 'background': props.backgroundColor = fx.val; break;
                        case 'custom': 
                            const customProps = this.parseCSSProps(fx.val);
                            Object.assign(props, customProps);
                            break;
                    }
                } else {
                    switch(fx.type) {
                        case 'scale': props.scale = 1; break;
                        case 'rotate': props.rotate = 0; break;
                        case 'translateX': props.x = 0; break;
                        case 'translateY': props.y = 0; break;
                        case 'parallax': 
                        case 'tilt':
                            props.x = 0; 
                            props.y = 0; 
                            props.rotateX = 0;
                            props.rotateY = 0;
                            break;
                        case 'opacity': props.opacity = 1; break;
                        case 'blur':
                        case 'grayscale':
                        case 'contrast':
                        case 'brightness': props.filter = 'none'; break;
                        case 'background': props.backgroundColor = ''; break;
                        case 'custom':
                            const customProps = this.parseCSSProps(fx.val);
                            Object.keys(customProps).forEach(key => props[key] = '');
                            break;
                    }
                }
            });
            return props;
        }

        parseCSSProps(cssString) {
            if (!cssString) return {};
            const props = {};
            const pairs = cssString.split(';');
            pairs.forEach(pair => {
                const [prop, val] = pair.split(':');
                if (prop && val) {
                    const camelProp = prop.trim().replace(/-([a-z])/g, (g) => g[1].toUpperCase());
                    props[camelProp] = val.trim();
                }
            });
            return props;
        }

        applyCustomStyles(el, cssString) {
            if (!el || !cssString) return;
            const customProps = this.parseCSSProps(cssString);
            Object.keys(customProps).forEach(prop => {
                el.style[prop] = customProps[prop];
            });
        }

        resetCustomStyles(el, cssString) {
            if (!el || !cssString) return;
            const customProps = this.parseCSSProps(cssString);
            Object.keys(customProps).forEach(prop => {
                el.style[prop] = '';
            });
        }

        bindGlobal() {
            // Listen for Elementor section/widget re-renders in editor
            if (window.elementorFrontend && elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/global', ($scope) => {
                    const el = $scope[0];
                    if (el && el.hasAttribute('data-hoversync')) {
                        try {
                            const data = JSON.parse(el.getAttribute('data-hoversync'));
                            if (data) this.setupElement(el, data);
                        } catch (e) {}
                    }
                    // Also scan children if it's a container
                    const children = el.querySelectorAll('[data-hoversync]');
                    children.forEach(child => {
                        try {
                            const data = JSON.parse(child.getAttribute('data-hoversync'));
                            if (data) this.setupElement(child, data);
                        } catch (e) {}
                    });
                });
            }
        }
    }

    // Launch
    window.HoverSync = new HoverSyncEngine();

})(jQuery);