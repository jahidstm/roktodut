@props(['captchaQuestion'])

<div class="p-4 rounded-2xl border border-slate-200 bg-slate-50">
    <label for="captcha_answer" class="text-sm font-extrabold text-slate-800">
        ক্যাপচা <span class="text-red-500">*</span>
    </label>

    <p class="mt-2 text-sm font-bold text-slate-700">
        {{ $captchaQuestion }}
    </p>

    <input
        id="captcha_answer"
        name="captcha_answer"
        type="text"
        value="{{ old('captcha_answer') }}"
        autocomplete="off"
        class="mt-3 w-full rounded-xl border-slate-200 bg-white px-4 py-3 font-bold focus:border-red-500 focus:ring-red-500"
        placeholder="উত্তর লিখুন"
    />

    @error('captcha_answer')
        <div class="mt-2 text-sm font-bold text-red-600">{{ $message }}</div>
    @enderror
</div>
