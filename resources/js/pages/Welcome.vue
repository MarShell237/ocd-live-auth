<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    ChevronDown,
    MapPin,
    Menu,
    Video,
    X,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import { dashboard, register } from '@/routes';
import { index as packagesIndex } from '@/routes/packages';

type Slot = {
    time: string;
    available: boolean;
};

const props = defineProps<{
    nextLiveDate?: string;
    slots?: Slot[];
}>();

const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
const primaryHref = computed(() =>
    isAuthenticated.value ? dashboard() : register(),
);

const menuOpen = ref(false);

/*
 * Créneaux de démonstration en attendant la gestion publique des créneaux.
 * Les propriétés `nextLiveDate` et `slots` permettent de brancher les
 * prochaines disponibilités sans modifier la page.
 */
const fallbackSlots: Slot[] = [
    { time: '18:00', available: true },
    { time: '19:00', available: true },
    { time: '20:00', available: false },
];

const slots = computed(() => props.slots ?? fallbackSlots);

const nextLiveDate = computed(
    () => props.nextLiveDate ?? nextSaturdayLabel(),
);

const steps = [
    {
        title: 'Choisissez votre mode',
        description: 'En ligne ou en présentiel au Togo.',
    },
    {
        title: 'Sélectionnez un package',
        description: 'Comparez les avantages et les tarifs.',
    },
    {
        title: 'Réservez un créneau',
        description: 'Choisissez la date et l’heure qui vous conviennent.',
    },
    {
        title: 'Passez en direct',
        description:
            'Recevez la confirmation par e-mail, puis présentez votre business.',
    },
];

const faq = [
    {
        question: 'Comment réserver un créneau ?',
        answer: 'Créez votre compte, choisissez votre mode de présentation, puis sélectionnez le package et le créneau qui vous conviennent.',
    },
    {
        question: 'Puis-je présenter mon business à distance ?',
        answer: 'Oui. Le mode En ligne vous permet de participer au direct TikTok où que vous soyez.',
    },
    {
        question: 'Quand vais-je recevoir la confirmation ?',
        answer: 'Votre demande est transmise directement au promoteur, qui confirme votre passage par e-mail.',
    },
    {
        question: 'Que se passe-t-il si je dois annuler ?',
        answer: 'Contactez-nous dès que possible : votre créneau pourra être proposé à un autre entrepreneur.',
    },
];

function nextSaturdayLabel(): string {
    const date = new Date();
    const daysUntilSaturday = (6 - date.getDay() + 7) % 7 || 7;
    date.setDate(date.getDate() + daysUntilSaturday);

    const label = new Intl.DateTimeFormat('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    }).format(date);

    return label.charAt(0).toUpperCase() + label.slice(1);
}
</script>

<template>
    <Head title="OCD Live — Présentez votre business">
        <meta
            name="description"
            content="Réservez en quelques minutes votre créneau de présentation commerciale, en ligne ou en présentiel au Togo."
        />
    </Head>

    <div class="min-h-screen bg-ocd-cream text-ocd-ink">
        <header class="sticky top-0 z-40 bg-ocd-cream/90 backdrop-blur-md">
            <div
                class="mx-auto flex h-20 max-w-[1560px] items-center justify-between gap-6 px-5 sm:px-8 lg:h-24 lg:px-14"
            >
                <a
                    href="/"
                    class="flex items-center gap-2.5"
                    aria-label="OCD Live, retour à l'accueil"
                >
                    <span
                        class="size-2.5 rounded-full bg-ocd-red"
                        aria-hidden="true"
                    />
                    <span
                        class="text-[21px] font-semibold tracking-[-0.02em] text-ocd-ink"
                    >
                        OCD Live
                    </span>
                </a>

                <div class="flex items-center gap-8">
                    <nav
                        class="hidden items-center gap-8 text-[15px] text-ocd-ink/85 lg:flex"
                        aria-label="Navigation principale"
                    >
                        <a
                            href="#comment-ca-marche"
                            class="transition-colors hover:text-ocd-green"
                        >
                            Comment ça marche
                        </a>
                        <Link
                            :href="packagesIndex()"
                            class="transition-colors hover:text-ocd-green"
                        >
                            Packages
                        </Link>
                        <a
                            href="#faq"
                            class="transition-colors hover:text-ocd-green"
                        >
                            Questions fréquentes
                        </a>
                    </nav>

                    <div class="flex items-center gap-2">
                        <Link
                            :href="primaryHref"
                            class="hidden rounded-full bg-ocd-green px-6 py-3 text-[15px] font-semibold text-white transition-colors hover:bg-ocd-green-dark sm:inline-flex"
                        >
                            Réserver un créneau
                        </Link>
                        <button
                            type="button"
                            class="inline-flex size-11 items-center justify-center rounded-full border border-ocd-line text-ocd-ink transition-colors hover:bg-white lg:hidden"
                            :aria-expanded="menuOpen"
                            aria-label="Ouvrir le menu"
                            @click="menuOpen = !menuOpen"
                        >
                            <X v-if="menuOpen" class="size-5" />
                            <Menu v-else class="size-5" />
                        </button>
                    </div>
                </div>
            </div>

            <div
                v-if="menuOpen"
                class="border-t border-ocd-line bg-ocd-cream px-5 py-5 sm:px-8 lg:hidden"
            >
                <nav class="flex flex-col gap-1 text-[15px]">
                    <a
                        href="#comment-ca-marche"
                        class="rounded-xl px-3 py-2.5 transition-colors hover:bg-white"
                        @click="menuOpen = false"
                    >
                        Comment ça marche
                    </a>
                    <Link
                        :href="packagesIndex()"
                        class="rounded-xl px-3 py-2.5 transition-colors hover:bg-white"
                        @click="menuOpen = false"
                    >
                        Packages
                    </Link>
                    <a
                        href="#faq"
                        class="rounded-xl px-3 py-2.5 transition-colors hover:bg-white"
                        @click="menuOpen = false"
                    >
                        Questions fréquentes
                    </a>
                </nav>
                <Link
                    :href="primaryHref"
                    class="mt-4 flex items-center justify-center rounded-full bg-ocd-green px-6 py-3 text-[15px] font-semibold text-white transition-colors hover:bg-ocd-green-dark"
                >
                    Réserver un créneau
                </Link>
            </div>
        </header>

        <section class="bg-ocd-cream">
            <div
                class="mx-auto grid max-w-[1560px] gap-14 px-5 pt-8 pb-20 sm:px-8 lg:grid-cols-[minmax(0,692px)_minmax(0,452px)] lg:items-center lg:justify-between lg:gap-16 lg:px-14 lg:pt-8 lg:pb-20"
            >
                <div>
                    <span
                        class="inline-flex items-center rounded-full bg-ocd-sun px-4 py-2 text-[14px] font-medium text-ocd-ink"
                    >
                        Directs TikTok de Bouvan
                    </span>

                    <h1
                        class="mt-8 max-w-[580px] text-[clamp(2.6rem,4.5vw,4.4rem)] leading-[1.05] font-semibold tracking-[-0.035em] text-ocd-ink"
                    >
                        Présentez votre business à toute la communauté.
                    </h1>

                    <p
                        class="mt-5 max-w-[560px] text-[19px] leading-[1.7] text-ocd-slate"
                    >
                        Réservez en quelques minutes votre créneau de
                        présentation commerciale, en ligne ou en présentiel au
                        Togo.
                    </p>

                    <div
                        class="mt-9 grid max-w-[692px] gap-6 sm:grid-cols-2"
                    >
                        <Link
                            :href="primaryHref"
                            class="group flex min-h-[187px] flex-col justify-between rounded-[22px] border border-ocd-ink/15 bg-white/60 p-7 text-left transition-colors hover:border-ocd-ink/30 hover:bg-white"
                        >
                            <div class="flex items-start justify-between">
                                <MapPin
                                    class="size-7 text-ocd-green"
                                    :stroke-width="1.8"
                                />
                                <ArrowRight
                                    class="size-5 text-ocd-ink transition-transform group-hover:translate-x-1"
                                />
                            </div>
                            <div class="mt-8">
                                <h2
                                    class="text-[21px] font-semibold tracking-[-0.01em] text-ocd-ink"
                                >
                                    En présentiel (Togo)
                                </h2>
                                <p
                                    class="mt-2 text-[15px] leading-relaxed text-ocd-slate"
                                >
                                    Présentez votre activité lors d'une session
                                    organisée au Togo.
                                </p>
                            </div>
                        </Link>

                        <Link
                            :href="primaryHref"
                            class="group flex min-h-[187px] flex-col justify-between rounded-[22px] bg-ocd-green p-7 text-left transition-colors hover:bg-ocd-green-dark"
                        >
                            <div class="flex items-start justify-between">
                                <Video
                                    class="size-7 text-white"
                                    :stroke-width="1.8"
                                />
                                <ArrowRight
                                    class="size-5 text-white transition-transform group-hover:translate-x-1"
                                />
                            </div>
                            <div class="mt-8">
                                <h2
                                    class="text-[21px] font-semibold tracking-[-0.01em] text-white"
                                >
                                    En ligne
                                </h2>
                                <p
                                    class="mt-2 text-[15px] leading-relaxed text-white/80"
                                >
                                    Passez à distance pendant un direct TikTok,
                                    où que vous soyez.
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>

                <div
                    class="w-full rounded-[28px] bg-ocd-ink p-7 text-white sm:p-8 lg:justify-self-end"
                >
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <span
                            class="rounded-full bg-ocd-red px-3 py-1 text-[11px] font-bold tracking-[0.08em] uppercase"
                        >
                            En direct
                        </span>
                        <span class="text-[14px] text-white/55">
                            {{ nextLiveDate }}
                        </span>
                    </div>

                    <h2
                        class="mt-8 text-[24px] font-semibold tracking-[-0.01em]"
                    >
                        Créneaux disponibles
                    </h2>

                    <div class="mt-5 space-y-3">
                        <div
                            v-for="slot in slots"
                            :key="slot.time"
                            class="flex items-center justify-between rounded-2xl px-5 py-3"
                            :class="
                                slot.available
                                    ? 'bg-ocd-row'
                                    : 'bg-ocd-row-muted text-white/45'
                            "
                        >
                            <span class="text-[18px]">{{ slot.time }}</span>
                            <span
                                class="flex items-center gap-2 text-[14px]"
                                :class="
                                    slot.available
                                        ? 'text-ocd-live'
                                        : 'text-white/40'
                                "
                            >
                                <span
                                    class="size-1.5 rounded-full"
                                    :class="
                                        slot.available
                                            ? 'bg-ocd-live'
                                            : 'bg-white/25'
                                    "
                                />
                                {{ slot.available ? 'Libre' : 'Complet' }}
                            </span>
                        </div>

                        <p
                            v-if="!slots.length"
                            class="rounded-2xl bg-ocd-row px-5 py-6 text-center text-[14px] text-white/55"
                        >
                            Les prochains créneaux seront annoncés très
                            bientôt.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section
            id="comment-ca-marche"
            class="scroll-mt-24 border-t border-ocd-line bg-white"
        >
            <div
                class="mx-auto max-w-[1560px] px-5 py-20 sm:px-8 lg:px-14 lg:py-24"
            >
                <h2
                    class="text-[clamp(2rem,3.2vw,2.75rem)] font-semibold tracking-[-0.03em] text-ocd-ink"
                >
                    Comment ça marche
                </h2>

                <ol
                    class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8"
                >
                    <li
                        v-for="(step, index) in steps"
                        :key="step.title"
                    >
                        <span
                            class="flex size-14 items-center justify-center rounded-full bg-ocd-mint text-[20px] font-semibold text-ocd-green"
                        >
                            {{ index + 1 }}
                        </span>
                        <h3
                            class="mt-6 text-[18px] font-semibold tracking-[-0.01em] text-ocd-ink"
                        >
                            {{ step.title }}
                        </h3>
                        <p
                            class="mt-2 max-w-[290px] text-[16px] leading-relaxed text-ocd-slate"
                        >
                            {{ step.description }}
                        </p>
                    </li>
                </ol>
            </div>
        </section>

        <section class="border-y border-ocd-line bg-ocd-cream">
            <div
                class="mx-auto flex max-w-[1560px] flex-col gap-8 px-5 py-16 sm:px-8 lg:flex-row lg:items-center lg:justify-between lg:px-14 lg:py-16"
            >
                <div>
                    <h2
                        class="text-[clamp(1.9rem,3vw,2.75rem)] font-semibold tracking-[-0.03em] text-ocd-ink"
                    >
                        Prêt à présenter votre business ?
                    </h2>
                    <p
                        class="mt-3 max-w-[640px] text-[18px] leading-relaxed text-ocd-slate"
                    >
                        Votre demande est transmise directement au promoteur,
                        qui confirme votre passage par e-mail.
                    </p>
                </div>

                <Link
                    :href="packagesIndex()"
                    class="inline-flex w-fit items-center justify-center rounded-full bg-ocd-ink px-10 py-5 text-[16px] font-semibold text-white transition-colors hover:bg-black"
                >
                    Voir les packages
                </Link>
            </div>
        </section>

        <section id="faq" class="scroll-mt-24 bg-white">
            <div
                class="mx-auto max-w-[1560px] px-5 py-20 sm:px-8 lg:px-14 lg:py-24"
            >
                <div
                    class="grid gap-12 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:gap-24"
                >
                    <div>
                        <h2
                            class="text-[clamp(2rem,3.2vw,2.75rem)] font-semibold tracking-[-0.03em] text-ocd-ink"
                        >
                            Questions fréquentes
                        </h2>
                        <p
                            class="mt-4 max-w-[380px] text-[16px] leading-relaxed text-ocd-slate"
                        >
                            Tout ce qu'il faut savoir avant de réserver votre
                            créneau.
                        </p>
                    </div>

                    <div
                        class="divide-y divide-ocd-line border-y border-ocd-line"
                    >
                        <details
                            v-for="item in faq"
                            :key="item.question"
                            class="group py-5"
                        >
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between gap-6 text-[16px] font-medium text-ocd-ink [&::-webkit-details-marker]:hidden"
                            >
                                {{ item.question }}
                                <ChevronDown
                                    class="size-5 shrink-0 text-ocd-slate transition-transform group-open:rotate-180"
                                />
                            </summary>
                            <p
                                class="mt-3 max-w-[620px] text-[15px] leading-relaxed text-ocd-slate"
                            >
                                {{ item.answer }}
                            </p>
                        </details>
                    </div>
                </div>
            </div>
        </section>

        <footer class="border-t border-ocd-line bg-ocd-cream">
            <div
                class="mx-auto flex max-w-[1560px] flex-col gap-5 px-5 py-10 text-[14px] text-ocd-slate sm:px-8 md:flex-row md:items-center md:justify-between lg:px-14"
            >
                <p>© OCD Live — Un projet de Bouvan</p>

                <nav
                    class="flex flex-wrap items-center gap-x-7 gap-y-2"
                    aria-label="Liens légaux"
                >
                    <a href="#" class="transition-colors hover:text-ocd-ink">
                        Politique de confidentialité
                    </a>
                    <a href="#" class="transition-colors hover:text-ocd-ink">
                        Mentions légales
                    </a>
                    <a
                        href="https://www.tiktok.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="transition-colors hover:text-ocd-ink"
                    >
                        TikTok
                    </a>
                </nav>
            </div>
        </footer>
    </div>
</template>
