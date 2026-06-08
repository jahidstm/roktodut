{{--
    ╔══════════════════════════════════════════════════════════════════╗
    ║  রক্তদূত AI Chatbot Widget — Upgraded                           ║
    ║  • Multi-turn history (LocalStorage, max 20 messages)           ║
    ║  • Quick Reply Chips                                             ║
    ║  • Structured Action Buttons from server                        ║
    ║  • Personalized (auth user context sent via HTTP)               ║
    ╚══════════════════════════════════════════════════════════════════╝
--}}
<div x-data="roktodutChat()" x-init="init()" class="fixed bottom-6 right-6 z-[9000]">

    {{-- ── FAB Toggle Button ── --}}
    <button @click="toggle()"
            aria-label="চ্যাট সহকারী"
            class="relative bg-[#D32F2F] hover:bg-red-700 text-white p-4 rounded-full shadow-2xl transition-all duration-200 hover:scale-105 flex items-center justify-center">
        {{-- Chat icon (closed state) --}}
        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        {{-- Close icon (open state) --}}
        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{-- Unread badge --}}
        <span x-show="unread > 0 && !open"
              x-text="unread"
              style="display:none;"
              class="absolute -top-1 -right-1 bg-amber-400 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow-sm">
        </span>
    </button>

    {{-- ── Chat Panel ── --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         style="display:none;"
         class="absolute bottom-16 right-0 w-[calc(100vw-2rem)] sm:w-[360px] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col"
         @click.outside="open = false">

        {{-- Header --}}
        <div class="bg-[#D32F2F] text-white px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-base">🩸</div>
                <div>
                    <p class="font-black text-sm leading-tight">রক্তদূত এআই</p>
                    <p class="text-[10px] text-red-200 font-medium">রক্তদান সহকারী</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="clearHistory()"
                        title="চ্যাট মুছুন"
                        class="text-red-200 hover:text-white transition text-xs font-bold px-2 py-1 rounded hover:bg-white/10">
                    মুছুন
                </button>
                <button @click="open = false" class="text-red-200 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Message Area --}}
        <div id="chat-messages"
             class="flex-1 overflow-y-auto bg-slate-50 p-3 flex flex-col gap-2 min-h-[240px] max-h-[320px]">

            {{-- Welcome message --}}
            <div class="flex items-start gap-2">
                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center text-xs shrink-0 mt-0.5">🩸</div>
                <div class="bg-white border border-gray-200 text-gray-800 px-3 py-2 rounded-r-xl rounded-bl-xl text-sm shadow-sm max-w-[85%] leading-relaxed">
                    হ্যালো! আমি রক্তদূত এআই। রক্তদান, স্বাস্থ্য বা ওয়েবসাইট সম্পর্কে যেকোনো প্রশ্ন করুন।
                </div>
            </div>

            {{-- Dynamic messages --}}
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex items-start gap-2" :class="msg.isUser ? 'flex-row-reverse' : 'flex-row'">
                    {{-- Bot avatar --}}
                    <template x-if="!msg.isUser">
                        <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center text-xs shrink-0 mt-0.5">🩸</div>
                    </template>

                    <div class="flex flex-col" :class="msg.isUser ? 'items-end' : 'items-start'">
                        {{-- Bubble --}}
                        <div :class="msg.isUser
                                ? 'bg-[#D32F2F] text-white rounded-l-xl rounded-br-xl'
                                : 'bg-white border border-gray-200 text-gray-800 rounded-r-xl rounded-bl-xl'"
                             class="px-3 py-2 text-sm shadow-sm max-w-[85%] leading-relaxed break-words">
                            <span x-text="msg.text"></span>
                        </div>

                        {{-- Action button (only on bot messages) --}}
                        <template x-if="!msg.isUser && msg.action">
                            <a :href="msg.action.url"
                               :class="{
                                   'bg-red-600 hover:bg-red-700 text-white': msg.action.style === 'danger',
                                   'bg-blue-600 hover:bg-blue-700 text-white': msg.action.style === 'primary',
                                   'bg-slate-700 hover:bg-slate-800 text-white': msg.action.style === 'info'
                               }"
                               class="mt-1.5 inline-block text-xs font-bold px-3 py-1.5 rounded-lg transition-colors shadow-sm">
                                <span x-text="msg.action.label"></span>
                            </a>
                        </template>

                        {{-- Timestamp --}}
                        <span class="text-[9px] text-slate-400 mt-0.5 px-1" x-text="msg.time"></span>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="isLoading" style="display:none;" class="flex items-start gap-2">
                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center text-xs shrink-0">🩸</div>
                <div class="bg-white border border-gray-200 px-3 py-2 rounded-r-xl rounded-bl-xl shadow-sm flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>
        </div>

        {{-- Quick Reply Chips --}}
        <div x-show="messages.length === 0 && !isLoading"
             class="px-3 pb-2 bg-slate-50 border-t border-slate-100 flex flex-wrap gap-1.5 pt-2 shrink-0">
            <template x-for="chip in chips" :key="chip">
                <button @click="sendChip(chip)"
                        class="text-xs font-semibold border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-full transition-colors">
                    <span x-text="chip"></span>
                </button>
            </template>
        </div>

        {{-- Input Area --}}
        <div class="p-3 bg-white border-t border-gray-100 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input x-model="userInput"
                       type="text"
                       :disabled="isLoading"
                       class="w-full border-gray-300 rounded-lg focus:ring-[#D32F2F] focus:border-[#D32F2F] text-sm px-3 py-2 disabled:opacity-60"
                       placeholder="আপনার প্রশ্ন লিখুন..."
                       autocomplete="off"
                       maxlength="1000">
                <button type="submit"
                        :disabled="isLoading || userInput.trim() === ''"
                        class="bg-[#D32F2F] hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold disabled:opacity-50 transition-colors shrink-0">
                    পাঠান
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('roktodutChat', () => ({
        open: false,
        userInput: '',
        isLoading: false,
        messages: [],
        unread: 0,

        chips: [
            'আমার রক্তের গ্রুপ কী?',
            'কোথায় donate করব?',
            'কতদিন পর রক্ত দিতে পারব?',
            'জরুরি রক্ত কীভাবে পাব?',
            'ডোনার কীভাবে খুঁজব?',
        ],

        // ── LocalStorage key ────────────────────────────────────────────────
        storageKey: 'roktodut_chat_v2',

        init() {
            this.loadHistory();
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.unread = 0;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        // ── History: LocalStorage ────────────────────────────────────────────
        loadHistory() {
            try {
                const raw = localStorage.getItem(this.storageKey);
                if (raw) {
                    const parsed = JSON.parse(raw);
                    if (Array.isArray(parsed)) {
                        this.messages = parsed.slice(-20); // keep last 20
                    }
                }
            } catch (_) {
                this.messages = [];
            }
        },

        saveHistory() {
            try {
                localStorage.setItem(this.storageKey, JSON.stringify(this.messages.slice(-20)));
            } catch (_) {}
        },

        clearHistory() {
            this.messages = [];
            localStorage.removeItem(this.storageKey);
        },

        // ── Normalize AI text ────────────────────────────────────────────────
        normalize(text) {
            return String(text ?? '')
                .replace(/\u00A0/g, ' ')
                .replace(/[*#`_]/g, '')
                .replace(/\r?\n+/g, ' ')
                .replace(/\s{2,}/g, ' ')
                .trim();
        },

        // ── Send chip shortcut ───────────────────────────────────────────────
        sendChip(chip) {
            this.userInput = chip;
            this.sendMessage();
        },

        // ── Current time label ───────────────────────────────────────────────
        timeLabel() {
            return new Date().toLocaleTimeString('bn-BD', { hour: '2-digit', minute: '2-digit' });
        },

        // ── Send message ─────────────────────────────────────────────────────
        sendMessage() {
            const message = this.normalize(this.userInput);
            if (message === '' || this.isLoading) return;

            this.messages.push({
                id: Date.now() + Math.random(),
                text: message,
                isUser: true,
                action: null,
                time: this.timeLabel(),
            });
            this.userInput = '';
            this.isLoading = true;
            this.saveHistory();
            this.$nextTick(() => this.scrollToBottom());

            fetch('{{ route("chatbot.ask") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: message,
                    // Send last 10 turns for context (server also caps at 10)
                    history: this.messages.slice(-10).map(m => ({
                        text: m.text,
                        isUser: m.isUser,
                    })),
                })
            })
            .then(async res => {
                let data = {};
                try { data = await res.json(); } catch (_) {}
                if (!res.ok) throw new Error(this.normalize(data.reply || 'উত্তর পাওয়া যায়নি।'));
                return data;
            })
            .then(data => {
                const reply  = this.normalize(data.reply || 'দুঃখিত, উত্তর পাওয়া যায়নি।');
                const action = data.action ?? null;

                this.messages.push({
                    id: Date.now() + Math.random(),
                    text: reply,
                    isUser: false,
                    action: action,
                    time: this.timeLabel(),
                });

                if (!this.open) this.unread++;
                this.saveHistory();
            })
            .catch(err => {
                this.messages.push({
                    id: Date.now() + Math.random(),
                    text: this.normalize(err.message || 'সার্ভারের সাথে যোগাযোগ করা যাচ্ছে না।'),
                    isUser: false,
                    action: null,
                    time: this.timeLabel(),
                });
            })
            .finally(() => {
                this.isLoading = false;
                this.$nextTick(() => this.scrollToBottom());
            });
        },

        scrollToBottom() {
            setTimeout(() => {
                const el = document.getElementById('chat-messages');
                if (el) el.scrollTop = el.scrollHeight;
            }, 80);
        },
    }));
});
</script>
