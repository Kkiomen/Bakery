<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Piekarnia Słodkie Ziarno</title>
    <meta name="description" content="Piekarnia rzemieślnicza: chleby na zakwasie, drożdżówki, ciasta i torty. Zamówienia detaliczne i B2B." />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-neutral-50 text-neutral-800 antialiased">
<!-- Top bar -->
<div class="bg-neutral-900 text-white text-sm">
    <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
        <p class="opacity-90">Pn–Pt 6:30–18:00 · Sb 7:00–14:00 · Nd zamknięte</p>
        <div class="flex items-center gap-4">
            <a href="{{ asset('/b2b/login') }}" class="underline decoration-dashed underline-offset-4 hover:opacity-90">B2B – zamów online</a>
            <a href="#kontakt" class="hover:opacity-90">Kontakt</a>
        </div>
    </div>
</div>

<div x-data="bakeryApp()" x-init="init()" class="min-h-screen flex flex-col">
    <template x-if="announcement.visible">
        <div class="bg-gradient-to-r from-fuchsia-600 via-rose-500 to-amber-400 text-white">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">
          <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold tracking-wide">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M12 3l7 6v12h-5v-6H10v6H5V9l7-6z"/></svg>
            Nowość dziś
          </span>
                <p class="text-sm md:text-base" x-text="announcement.message"></p>
                <button @click="dismissAnnouncement()" class="ml-auto rounded-full bg-white/15 px-3 py-1 text-xs hover:bg-white/25 transition">
                    Zamknij
                </button>
            </div>
        </div>
    </template>

    <!-- Nawigacja -->
    <header x-data="{ atTop: true }" x-init="() => { window.addEventListener('scroll', () => atTop = window.scrollY < 10) }" :class="atTop ? 'bg-white/80' : 'bg-white shadow'" class="sticky top-0 z-40 backdrop-blur">
        <nav class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="#hero" class="flex items-center gap-2 font-bold">
                    <span class="text-xl">🥖</span>
                    <span>Piekarnia <span class="text-amber-600">Słodkie Ziarno</span></span>
                </a>
                <ul class="hidden md:flex items-center gap-8 text-sm">
                    <li><a href="#chleby" class="hover:text-amber-700">Chleby</a></li>
                    <li><a href="#drozdzowki" class="hover:text-amber-700">Drożdżówki</a></li>
                    <li><a href="#ciasta" class="hover:text-amber-700">Ciasta</a></li>
                    <li><a href="#torty" class="hover:text-amber-700">Torty</a></li>
                    <li><a href="#galeria" class="hover:text-amber-700">Galeria</a></li>
                    <li><a href="#b2b" class="hover:text-amber-700">B2B</a></li>
                    <li><a href="#kontakt" class="hover:text-amber-700">Kontakt</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- HERO -->
    <section id="hero" class="relative">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-8 items-center px-4 py-16">
            <div>
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-tight">
                    Chleby na zakwasie, drożdżówki, ciasta i <span class="text-amber-600">torty</span>
                </h1>
                <p class="mt-4 text-neutral-600 text-lg">Codziennie wypiekane z najlepszych składników. Zamów online, odbierz w piekarni lub skorzystaj z dostaw B2B.</p>
            </div>
            <div class="relative" x-data="carousel(4000)">
                <div class="relative w-full h-72 md:h-96 rounded-2xl overflow-hidden shadow-lg">
                    <img src="{{ asset('assets/images/piekarnia_opoczno.jpg') }}"  class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700" />
                </div>
            </div>
        </div>
    </section>

    <!-- Chleby -->
    <section id="chleby" class="py-16">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-6 items-center">
            <img src="{{ asset('assets/images/piekarnia_rodzaje.jpg') }}" alt="Chleb rzemieślniczy" class="rounded-2xl shadow object-cover h-64 w-full" />
            <div>
                <h2 class="text-3xl font-bold">Chleby rzemieślnicze</h2>
                <p class="mt-3 text-neutral-700">Na zakwasie, pszenne, żytnie, z ziarnami. Aromatyczne i zawsze świeże.</p>
            </div>
        </div>
    </section>

    <!-- Drożdżówki -->
    <section id="drozdzowki" class="py-16 bg-neutral-100">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-6 items-center">
            <div>
                <h2 class="text-3xl font-bold">Drożdżówki</h2>
                <p class="mt-3 text-neutral-700">Sezonowe nadzienia: jagoda, ser, mak, jabłko – zawsze pełne smaku i puszyste.</p>
            </div>
            <img src="{{ asset('assets/images/drozdzkowki_opoczno.jpg') }}" alt="Drożdżówki" class="rounded-2xl shadow object-cover h-64 w-full" />
        </div>
    </section>

    <!-- Ciasta -->
    <section id="ciasta" class="py-16">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-6 items-center">
            <img src="{{ asset('assets/images/ciasta.jpg') }}" alt="Ciasta" class="rounded-2xl shadow object-cover object-top h-64 w-full" />
            <div>
                <h2 class="text-3xl font-bold">Ciasta</h2>
                <p class="mt-3 text-neutral-700">Serniki, bezy, tarty, brownie – dostępne na wagę i w porcjach, idealne na każdą okazję.</p>
            </div>
        </div>
    </section>

    <!-- Torty -->
    <section id="torty" class="py-16 bg-neutral-100">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-6 items-center">
            <div>
                <h2 class="text-3xl font-bold">Torty</h2>
                <p class="mt-3 text-neutral-700">Na urodziny, wesela i inne uroczystości – personalizowane smaki i dekoracje według Twoich potrzeb.</p>
            </div>
            <img src="{{ asset('assets/images/torty.jpg') }}" alt="Torty" class="rounded-2xl shadow object-cover object-top h-64 w-full" />
        </div>
    </section>

    <!-- Galeria -->
    <section id="galeria" class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold">Galeria wypieków</h2>
            <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <template x-for="img in gallery" :key="img">
                    <a :href="img" target="_blank" class="group overflow-hidden rounded-2xl block bg-white">
                        <img :src="img" alt="Wypiek" class="h-48 w-full object-cover group-hover:scale-105 transition" />
                    </a>
                </template>
            </div>
        </div>
    </section>

    <!-- B2B -->
    <section id="b2b" class="py-16 bg-neutral-100">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-3xl font-bold">Współpraca B2B</h2>
                <p class="mt-3 text-neutral-700">Zaopatrujemy kawiarnie, hotele i sklepy. Oferujemy stałe rabaty, harmonogramy dostaw i panel zamówień online.</p>
                <ul class="mt-4 space-y-2 text-sm list-disc pl-5 text-neutral-700">
                    <li>Dedykowane ceny i faktury zbiorcze</li>
                    <li>Dostawy poranne i popołudniowe</li>
                    <li>Asortyment stały i sezonowy</li>
                </ul>
            </div>
            <img src="https://images.unsplash.com/photo-1482938289607-e9573fc25ebb?q=80&w=1200&auto=format&fit=crop" alt="Dostawa do kawiarni" class="rounded-2xl shadow object-cover h-72 w-full" />
        </div>
    </section>

    <!-- Opinie -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8">Opinie klientów</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <figure class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center text-center">
                    <img src="https://randomuser.me/api/portraits/women/1.jpg" alt="Ania" class="w-16 h-16 rounded-full mb-4 shadow" />
                    <blockquote class="text-neutral-700 italic">„Najlepsze jagodzianki w mieście – chrupiące i pełne owoców!”</blockquote>
                    <figcaption class="mt-3 font-semibold">Ania</figcaption>
                </figure>
                <figure class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center text-center">
                    <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="Marek" class="w-16 h-16 rounded-full mb-4 shadow" />
                    <blockquote class="text-neutral-700 italic">„Chleb na zakwasie jak dawniej. Pachnący i długo świeży.”</blockquote>
                    <figcaption class="mt-3 font-semibold">Marek</figcaption>
                </figure>
                <figure class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center text-center">
                    <img src="https://randomuser.me/api/portraits/women/3.jpg" alt="Julia" class="w-16 h-16 rounded-full mb-4 shadow" />
                    <blockquote class="text-neutral-700 italic">„Tort urodzinowy wyglądał obłędnie i smakował jeszcze lepiej.”</blockquote>
                    <figcaption class="mt-3 font-semibold">Julia</figcaption>
                </figure>
            </div>
        </div>
    </section>

    <!-- Kontakt -->
    <section id="kontakt" class="py-16 bg-neutral-100">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-3xl font-bold">Kontakt</h2>
                <p class="mt-3">ul. Rzemieślnicza 12, 00-000 Miasto</p>
                <p class="mt-1">tel. 123 456 789 · <a href="mailto:kontakt@slodkieziarno.pl" class="underline">kontakt@slodkieziarno.pl</a></p>
                <p class="mt-1">Zachęcamy do kontaktu telefonicznego – szybciej odpowiemy!</p>
            </div>
            <img src="https://images.unsplash.com/photo-1519666213637-9ad625c4a07e?q=80&w=1200&auto=format&fit=crop" alt="Mapa okolicy" class="rounded-2xl shadow object-cover h-72 w-full" />
        </div>
    </section>

    <!-- Stopka -->
    <footer class="mt-auto bg-neutral-900 text-neutral-200">
        <div class="max-w-7xl mx-auto px-4 py-10 grid md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <p class="font-semibold">Piekarnia Słodkie Ziarno</p>
                <p class="text-sm opacity-80 mt-2">Rzemieślnicze wypieki od 1998 roku. Naturalne składniki, własny zakwas, zero kompromisów.</p>
            </div>
            <div>
                <p class="font-semibold">Nawigacja</p>
                <ul class="text-sm mt-2 space-y-2">
                    <li><a href="#chleby" class="hover:underline">Chleby</a></li>
                    <li><a href="#drozdzowki" class="hover:underline">Drożdżówki</a></li>
                    <li><a href="#ciasta" class="hover:underline">Ciasta</a></li>
                    <li><a href="#torty" class="hover:underline">Torty</a></li>
                    <li><a href="#galeria" class="hover:underline">Galeria</a></li>
                    <li><a href="#b2b" class="hover:underline">B2B</a></li>
                </ul>
            </div>
        </div>

        <!-- Dane testowe -->
        <div class="border-t border-white/10 bg-white/5">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <h3 class="font-semibold text-lg mb-4">🧪 Dane testowe do systemu</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="bg-white/10 rounded-lg p-4">
                        <h4 class="font-semibold mb-3">👨‍💼 Panel Administratora</h4>
                        <p class="mb-2"><strong>URL:</strong> <a href="/login" class="text-yellow-300 hover:underline">/login</a></p>
                        <p class="mb-1"><strong>Email:</strong> <code class="bg-white/20 px-2 py-1 rounded">admin@piekarnia.pl</code></p>
                        <p class="mb-3"><strong>Hasło:</strong> <code class="bg-white/20 px-2 py-1 rounded">admin123</code></p>
                        <p class="text-xs opacity-80">Dostęp do zarządzania produkcją, klientami B2B, dostawami</p>
                    </div>

                    <div class="bg-white/10 rounded-lg p-4">
                        <h4 class="font-semibold mb-3">🏢 Portal B2B (przykładowe konto)</h4>
                        <p class="mb-2"><strong>URL:</strong> <a href="/b2b/login" class="text-yellow-300 hover:underline">/b2b/login</a></p>
                        <p class="mb-1"><strong>Email:</strong> <code class="bg-white/20 px-2 py-1 rounded">zamowienia@grandpalace.pl</code></p>
                        <p class="mb-3"><strong>Hasło:</strong> <code class="bg-white/20 px-2 py-1 rounded">password123</code></p>
                        <p class="text-xs opacity-80">Hotel Grand Palace - poziom Złoty, zamówienia cykliczne</p>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-yellow-500/20 border border-yellow-500/30 rounded-lg">
                    <h4 class="font-semibold mb-2">🎭 Funkcja przełączania dla administratora</h4>
                    <p class="text-sm">Po zalogowaniu jako administrator, wejdź na <a href="/admin/impersonate" class="text-yellow-300 hover:underline">/admin/impersonate</a> aby przełączyć się na dowolne konto B2B i testować system z perspektywy klienta.</p>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 py-4 text-xs opacity-70">© <span x-text="new Date().getFullYear()"></span> Słodkie Ziarno. Wszelkie prawa zastrzeżone.</div>
        </div>
    </footer>
</div>
</body>
