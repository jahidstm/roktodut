@extends('layouts.app')

@section('title', 'ডোনার চ্যাট — রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data='chatRoom(@json($messages), {{ (int) auth()->id() }}, "{{ e($storeUrl) }}", {{ (int) $response->id }}, {{ $isClosed ? "true" : "false" }})'
     x-init="init()">

    <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-900">রিয়েল-টাইম চ্যাট</h1>
                        <p class="text-sm text-slate-500 font-semibold mt-1">ডোনার এবং রিকোয়েস্টার—একসাথে দ্রুত যোগাযোগ</p>
                    </div>
                    <a href="tel:{{ $oppositePartyPhone }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-extrabold text-red-700 hover:bg-red-100 transition {{ $oppositePartyPhone ? '' : 'pointer-events-none opacity-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.129a11.042 11.042 0 005.516 5.516l1.129-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        অডিও কল করুন
                    </a>
                </div>

                @if($isClosed)
                    <div class="mx-6 mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                        এই রক্তদান প্রক্রিয়াটি সম্পন্ন/বাতিল হওয়ায় চ্যাটটি বন্ধ করা হয়েছে।
                    </div>
                @endif

                <div class="px-6 py-6 bg-slate-50">
                    <div x-ref="messages"
                         class="h-[420px] sm:h-[520px] overflow-y-auto space-y-4 pr-1">
                        <template x-if="messages.length === 0">
                            <div class="text-center text-slate-400 text-sm font-semibold py-8">
                                এখনো কোনো মেসেজ নেই। শুরু করুন!
                            </div>
                        </template>

                        <template x-for="msg in messages" :key="msg.id">
                            <div class="flex" :class="isMine(msg) ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[80%] px-4 py-2.5 shadow-sm transition-all duration-300"
                                     :class="[
                                         isMine(msg)
                                            ? 'bg-red-600 text-white rounded-l-2xl rounded-br-2xl'
                                            : 'bg-white border border-slate-200 text-slate-900 rounded-r-2xl rounded-bl-2xl',
                                         msg.is_sending ? 'opacity-60' : 'opacity-100'
                                     ]">
                                    <p class="text-sm font-semibold whitespace-pre-line" x-text="msg.message"></p>
                                    <div class="mt-1 flex items-center justify-end gap-1 text-[10px] font-semibold opacity-70">
                                        <span x-text="formatTime(msg.created_at)"></span>
                                        <template x-if="msg.is_sending">
                                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                @unless($isClosed)
                    <div class="px-6 py-4 border-t border-slate-100 bg-white space-y-3">
                        <div class="flex flex-wrap gap-2">
                            @if($isRequester)
                                <button type="button"
                                        @click="sendQuick('ব্লাড ব্যাগ রেডি আছে, আপনি কতদূর?')"
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                    ব্লাড ব্যাগ রেডি আছে, আপনি কতদূর?
                                </button>
                                <button type="button"
                                        @click="sendQuick('রোগীর অবস্থা ইমার্জেন্সি, দ্রুত আসুন')"
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                    রোগীর অবস্থা ইমার্জেন্সি, দ্রুত আসুন
                                </button>
                            @else
                                <button type="button"
                                        @click="sendQuick('আমি ১৫ মিনিটের মধ্যে পৌঁছাচ্ছি')"
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                    আমি ১৫ মিনিটের মধ্যে পৌঁছাচ্ছি
                                </button>
                                <button type="button"
                                        @click="sendQuick('আমি হসপিটালে পৌঁছে গেছি')"
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                    আমি হসপিটালে পৌঁছে গেছি
                                </button>
                            @endif
                        </div>

                        <form @submit.prevent="sendMessage()"
                              class="flex items-center gap-3">
                            <input type="text"
                                   x-model="newMessage"
                                   placeholder="আপনার মেসেজ লিখুন..."
                                   class="flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold focus:border-red-400 focus:ring-red-400">
                            <button type="submit"
                                    :disabled="sending"
                                    class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-red-700 disabled:opacity-50 transition">
                                পাঠান
                            </button>
                        </form>
                    </div>
                @endunless
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatRoom', (initialMessages, userId, postUrl, responseId, isClosed) => ({
            messages: Array.isArray(initialMessages) ? initialMessages : [],
            userId,
            postUrl,
            responseId,
            isClosed,
            newMessage: '',
            sending: false,
            messageIds: new Set((initialMessages || []).map(m => m.id)),
            init() {
                this.scrollToBottom();
                this.bindEcho();
            },
            bindEcho() {
                if (!window.Echo) {
                    console.warn('[Chat] Echo not available — make sure Reverb is running.');
                    return;
                }
                window.Echo
                    .private(`chat.response.${this.responseId}`)
                    .listen('.MessageSent', (event) => {
                        if (!event || this.messageIds.has(event.id)) {
                            return;
                        }
                        this.messages = [...this.messages, {
                            id: event.id,
                            sender_id: event.sender_id,
                            message: event.message,
                            created_at: event.created_at,
                        }];
                        this.messageIds.add(event.id);
                        this.scrollToBottom();
                    });
            },
            async sendMessage(overrideText = null) {
                if (this.isClosed) return;
                
                const isEvent = overrideText && typeof overrideText === 'object' && overrideText instanceof Event;
                const text = String(!isEvent && overrideText ? overrideText : this.newMessage).trim();
                
                if (!text || this.sending) return;

                const tempId = 'temp_' + Date.now();
                const tempMessage = {
                    id: tempId,
                    sender_id: this.userId,
                    message: text,
                    created_at: new Date().toISOString(),
                    is_sending: true
                };

                this.messages = [...this.messages, tempMessage];
                this.messageIds.add(tempId);
                this.newMessage = ''; 
                this.scrollToBottom();

                this.sending = true;

                try {
                    const response = await window.axios.post(this.postUrl, { message: text });
                    const data = response.data;

                    if (data?.message) {
                        // Ensure we aren't duplicating if Echo beat us to it
                        if (!this.messageIds.has(data.message.id)) {
                            this.messages = this.messages.map(m => m.id === tempId ? data.message : m);
                            this.messageIds.delete(tempId);
                            this.messageIds.add(data.message.id);
                        } else {
                            // Echo already added it, just remove our temp
                            this.messages = this.messages.filter(m => m.id !== tempId);
                            this.messageIds.delete(tempId);
                        }
                    }

                    this.scrollToBottom();
                } catch (error) {
                    console.error('[Chat] Send failed:', error);
                    this.messages = this.messages.filter(m => m.id !== tempId);
                    this.messageIds.delete(tempId);
                    if (!this.newMessage) {
                        this.newMessage = text;
                    }
                    const errMsg = error.response?.data?.message || error.message || 'মেসেজ পাঠানো যায়নি।';
                    alert(errMsg);
                } finally {
                    this.sending = false;
                }
            },
            sendQuick(text) {
                this.sendMessage(text);
            },
            isMine(msg) {
                return Number(msg?.sender_id) === Number(this.userId);
            },
            formatTime(timestamp) {
                if (!timestamp) return '';
                const date = new Date(timestamp);
                return date.toLocaleTimeString('bn-BD', { hour: '2-digit', minute: '2-digit' });
            },
            scrollToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.messages;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            },
        }));
    });
</script>
@endsection
