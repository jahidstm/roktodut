{{-- Terms of Service TOC — included by both desktop sidebar and mobile accordion --}}
@php
$termsLinks = [
    ['href' => '#platform-nature', 'label' => 'প্ল্যাটফর্মের পরিচয়'],
    ['href' => '#acceptance',      'label' => 'শর্ত গ্রহণ ও যোগ্যতা'],
    ['href' => '#account',         'label' => 'অ্যাকাউন্ট দায়িত্ব'],
    ['href' => '#donor-rules',     'label' => 'ডোনারের দায়িত্ব'],
    ['href' => '#recipient-rules', 'label' => 'রক্তগ্রহীতার দায়িত্ব'],
    ['href' => '#prohibited',      'label' => 'নিষিদ্ধ কার্যক্রম'],
    ['href' => '#termination',     'label' => 'সাসপেনশন ও ব্যান'],
    ['href' => '#liability',       'label' => 'দায়বদ্ধতার সীমা'],
    ['href' => '#governing-law',   'label' => 'প্রযোজ্য আইন'],
];
@endphp
<ul class="space-y-0.5">
    @foreach($termsLinks as $link)
    <li>
        <a href="{{ $link['href'] }}"
           class="flex items-center gap-2 px-3 py-2 text-sm text-slate-600 font-medium rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors group">
            <span class="w-1 h-1 rounded-full bg-slate-300 group-hover:bg-red-400 transition-colors shrink-0"></span>
            {{ $link['label'] }}
        </a>
    </li>
    @endforeach
</ul>
