/**
 * KSG Innovation Portal - Hero Slider Engine
 * Vanilla JavaScript (Zero Dependencies)
 */
(function () {
    'use strict';

    var FADE_MS = 800;
    var SWIPE_MIN_PX = 40;

    function HeroSlider(root) {
        var self = this;
        this.root = root;
        this.slides = Array.prototype.slice.call(root.querySelectorAll('.hero-slide'));
        this.dots = Array.prototype.slice.call(root.querySelectorAll('.hero-dot'));
        this.prevBtn = root.querySelector('.hero-prev');
        this.nextBtn = root.querySelector('.hero-next');
        this.toggleBtn = root.querySelector('.hero-toggle');
        this.interval = Math.max(3000, parseInt(root.getAttribute('data-interval'), 10) || 6000);
        this.index = 0;
        this.timer = null;
        this.leaveTimers = new Map();
        this.userPaused = false;
        this.hoverPaused = false;
        this.focusPaused = false;
        this.tabHidden = document.hidden === true;

        if (this.slides.length <= 1) {
            if (this.prevBtn) this.prevBtn.style.display = 'none';
            if (this.nextBtn) this.nextBtn.style.display = 'none';
            if (this.toggleBtn) this.toggleBtn.style.display = 'none';
            var bar = root.querySelector('.hero-bar');
            if (bar) bar.style.display = 'none';
            return;
        }

        // Attach listeners
        root.addEventListener('mouseenter', function () {
            self.hoverPaused = true;
            self.schedule();
        });

        root.addEventListener('mouseleave', function () {
            self.hoverPaused = false;
            self.schedule();
        });

        root.addEventListener('focusin', function () {
            self.focusPaused = true;
            self.schedule();
        });

        root.addEventListener('focusout', function (e) {
            if (!self.root.contains(e.relatedTarget)) {
                self.focusPaused = false;
                self.schedule();
            }
        });

        document.addEventListener('visibilitychange', function () {
            self.tabHidden = document.hidden === true;
            self.schedule();
        });

        // Keyboard arrow navigation
        window.addEventListener('keydown', function (e) {
            if (e.target && /input|textarea|select/i.test(e.target.tagName)) return;
            if (e.key === 'ArrowLeft') {
                self.prev();
            } else if (e.key === 'ArrowRight') {
                self.next();
            }
        });

        // Touch Swipe
        var touchStartX = 0;
        var touchStartY = 0;
        root.addEventListener('touchstart', function (e) {
            if (e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        root.addEventListener('touchend', function (e) {
            if (e.changedTouches.length === 1) {
                var deltaX = e.changedTouches[0].clientX - touchStartX;
                var deltaY = e.changedTouches[0].clientY - touchStartY;
                if (Math.abs(deltaX) > SWIPE_MIN_PX && Math.abs(deltaX) > Math.abs(deltaY) * 1.5) {
                    if (deltaX < 0) {
                        self.next();
                    } else {
                        self.prev();
                    }
                }
            }
        }, { passive: true });

        // Button Clicks
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                self.prev();
            });
        }

        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                self.next();
            });
        }

        if (this.toggleBtn) {
            this.toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                self.userPaused = !self.userPaused;
                self.updateToggleBtn();
                self.schedule();
            });
        }

        // Dot Navigation
        this.dots.forEach(function (dot, i) {
            dot.addEventListener('click', function (e) {
                e.preventDefault();
                self.goTo(i);
            });
        });

        this.goTo(0, true);
        this.schedule();
    }

    HeroSlider.prototype.updateToggleBtn = function () {
        if (!this.toggleBtn) return;
        var isPaused = this.userPaused;
        this.toggleBtn.setAttribute('aria-label', isPaused ? 'Play slider' : 'Pause slider');
        this.toggleBtn.innerHTML = isPaused
            ? '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M4.5 3.5v13l11-6.5-11-6.5z"/></svg>'
            : '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4h3v12H5V4zm7 0h3v12h-3V4z"/></svg>';
    };

    HeroSlider.prototype.schedule = function () {
        var self = this;
        clearTimeout(this.timer);
        this.timer = null;

        var shouldPause = this.userPaused || this.hoverPaused || this.focusPaused || this.tabHidden;
        if (shouldPause) return;

        this.timer = setTimeout(function () {
            self.next();
        }, this.interval);
    };

    HeroSlider.prototype.goTo = function (targetIndex, immediate) {
        var count = this.slides.length;
        if (count === 0) return;

        var nextIndex = ((targetIndex % count) + count) % count;
        if (nextIndex === this.index && !immediate) return;

        var outgoing = this.slides[this.index];
        var incoming = this.slides[nextIndex];

        // Clear any lingering timer on outgoing
        var existingTimer = this.leaveTimers.get(outgoing);
        if (existingTimer) {
            clearTimeout(existingTimer);
            this.leaveTimers.delete(outgoing);
        }

        if (outgoing && !immediate) {
            outgoing.classList.remove('is-active');
            outgoing.classList.add('is-leaving');
            var leaveTimer = setTimeout(function () {
                outgoing.classList.remove('is-leaving');
            }, FADE_MS);
            this.leaveTimers.set(outgoing, leaveTimer);
        }

        // Activate incoming
        incoming.classList.remove('is-leaving');
        incoming.classList.add('is-active');

        // Update dots
        this.dots.forEach(function (dot, i) {
            var isCurrent = i === nextIndex;
            dot.classList.toggle('is-current', isCurrent);
            dot.setAttribute('aria-current', isCurrent ? 'true' : 'false');
        });

        this.index = nextIndex;
        this.schedule();
    };

    HeroSlider.prototype.next = function () {
        this.goTo(this.index + 1);
    };

    HeroSlider.prototype.prev = function () {
        this.goTo(this.index - 1);
    };

    // Auto-initialize when DOM is loaded
    function init() {
        var sliders = document.querySelectorAll('.hero-slider');
        sliders.forEach(function (el) {
            new HeroSlider(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
