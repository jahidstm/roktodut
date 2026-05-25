@props(['value', 'duration' => 2000, 'format' => false])

<span
    x-data="{
        current: 0,
        target: {{ is_numeric($value) ? $value : 0 }},
        duration: {{ $duration }},
        startTime: null,
        format: {{ $format ? 'true' : 'false' }},
        reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,

        init() {
            if (this.target === 0 || this.reducedMotion) {
                this.current = this.target;
                return;
            }
            
            let observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    window.requestAnimationFrame(this.step.bind(this));
                    observer.disconnect();
                }
            });
            observer.observe(this.$el);
        },

        step(timestamp) {
            if (!this.startTime) this.startTime = timestamp;
            let progress = timestamp - this.startTime;

            // easeOutExpo logic
            let easing = (progress >= this.duration) ? 1 : 1 - Math.pow(2, -10 * progress / this.duration);
            
            if (progress < this.duration) {
                this.current = Math.min(this.target, Math.round(this.target * easing));
                window.requestAnimationFrame(this.step.bind(this));
            } else {
                this.current = this.target;
            }
        },

        get formattedValue() {
            if (!this.format) return this.current;
            return new Intl.NumberFormat('en-US').format(this.current);
        }
    }"
    x-text="formattedValue"
>
    {{ $format ? number_format($value) : $value }}
</span>
