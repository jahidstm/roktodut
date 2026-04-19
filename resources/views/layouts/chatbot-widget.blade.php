<div x-data="chatbot()" class="fixed bottom-16 right-8 z-[9000]">
    <button @click="open = !open" class="bg-[#D32F2F] hover:bg-red-700 text-white p-4 rounded-full shadow-2xl transition-transform hover:scale-105 flex items-center justify-center">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
    </button>

    <div x-show="open" @click.outside="open = false" style="display: none;" class="absolute bottom-16 right-0 w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col">
        <div class="bg-[#D32F2F] text-white p-4 font-bold flex justify-between items-center">
            <span>রক্তদূত এআই সহকারী</span>
            <button @click="open = false" class="text-white hover:text-gray-200 text-xl font-bold">&times;</button>
        </div>

        <div id="chat-messages" class="p-4 h-72 overflow-y-auto bg-slate-50 flex flex-col gap-2">
            <div class="bg-white border border-gray-200 text-gray-800 p-3 rounded-r-xl rounded-bl-xl text-sm w-fit max-w-[85%] shadow-sm">
                হ্যালো! আমি রক্তদূত এআই। রক্তদান, সাধারণ স্বাস্থ্য সচেতনতা বা ওয়েবসাইট ব্যবহারের গাইড নিয়ে প্রশ্ন করতে পারেন।
            </div>

            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.isUser ? 'bg-[#D32F2F] text-white self-end rounded-l-xl rounded-br-xl' : 'bg-white border border-gray-200 text-gray-800 self-start rounded-r-xl rounded-bl-xl'" class="p-3 text-sm w-fit max-w-[85%] shadow-sm whitespace-normal break-words text-left leading-relaxed">
                    <span x-text="msg.text"></span>
                </div>
            </template>

            <div x-show="isLoading" class="text-xs text-gray-500 self-start animate-pulse bg-gray-200 px-3 py-1 rounded-full mt-2">
                এআই উত্তর তৈরি করছে...
            </div>
        </div>

            <div class="p-3 bg-white border-t border-gray-100">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input x-model="userInput" type="text" class="w-full border-gray-300 rounded-lg focus:ring-[#D32F2F] focus:border-[#D32F2F] text-sm px-3 py-2 text-left indent-0" placeholder="আপনার প্রশ্ন লিখুন..." autocomplete="off">
                    <button type="submit" :disabled="isLoading" class="bg-[#1e1e24] text-white px-4 py-2 rounded-lg text-sm disabled:opacity-50 hover:bg-gray-800 transition-colors">পাঠান</button>
                </form>
            </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatbot', () => ({
            open: false,
            userInput: '',
            isLoading: false,
            messages: [],
            normalizeText(text) {
                return String(text ?? '')
                    .replace(/\u00A0/g, ' ')
                    .replace(/[*#`_]/g, '')
                    .replace(/\r?\n+/g, ' ')
                    .replace(/\s{2,}/g, ' ')
                    .trim();
            },
            sendMessage() {
                const message = this.normalizeText(this.userInput);
                if (message === '' || this.isLoading) return;

                this.messages.push({ id: Date.now() + Math.random(), text: message, isUser: true });
                this.userInput = '';
                this.isLoading = true;
                this.scrollToBottom();

                fetch('{{ route("chatbot.ask") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message })
                })
                .then(async response => {
                    let data = {};
                    try {
                        data = await response.json();
                    } catch (_) {
                        data = {};
                    }
                    if (!response.ok) {
                        throw new Error(this.normalizeText(data.reply || 'দুঃখিত, উত্তর পাওয়া যায়নি।'));
                    }
                    return data;
                })
                .then(data => {
                    const replyText = this.normalizeText(data.reply || 'দুঃখিত, এই মুহূর্তে উত্তর পাওয়া যায়নি।');
                    this.messages.push({
                        id: Date.now() + Math.random(),
                        text: replyText,
                        isUser: false
                    });
                })
                .catch((error) => {
                    const errorText = this.normalizeText(error.message || 'দুঃখিত, সার্ভারের সাথে যোগাযোগ করা যাচ্ছে না। একটু পরে আবার চেষ্টা করুন।');
                    this.messages.push({
                        id: Date.now() + Math.random(),
                        text: errorText,
                        isUser: false
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                    this.scrollToBottom();
                });
            },
            scrollToBottom() {
                setTimeout(() => {
                    const chatDiv = document.getElementById('chat-messages');
                    if (chatDiv) {
                        chatDiv.scrollTop = chatDiv.scrollHeight;
                    }
                }, 100);
            }
        }));
    });
</script>
