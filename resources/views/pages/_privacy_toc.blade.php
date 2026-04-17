{{-- Privacy Policy TOC — included by both desktop sidebar and mobile accordion --}}
@php
$privacyLinks = [
    ['href' => '#intro',          'label' => 'ভূমিকা'],
    ['href' => '#data-collection','label' => 'তথ্য সংগ্রহ'],
    ['href' => '#data-use',       'label' => 'তথ্য ব্যবহার'],
    ['href' => '#phone-reveal',   'label' => 'ফোন নম্বর প্রাইভেসি'],
    ['href' => '#nid-security',   'label' => 'NID ডকুমেন্ট সুরক্ষা'],
    ['href' => '#cookies',        'label' => 'কুকিজ ও সেশন'],
    ['href' => '#user-rights',    'label' => 'আপনার অধিকার'],
    ['href' => '#policy-updates', 'label' => 'নীতিমালা আপডেট'],
    ['href' => '#contact-us',     'label' => 'যোগাযোগ'],
];
@endphp
<ul class="space-y-0.5">
    @foreach($privacyLinks as $link)
    <li>
        <a href="{{ $link['href'] }}"
           class="flex items-center gap-2 px-3 py-2 text-sm text-slate-600 font-medium rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors group">
            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover:bg-red-400 transition-colors shrink-0"></span>
            {{ $link['label'] }}
        </a>
    </li>
    @endforeach
</ul>
