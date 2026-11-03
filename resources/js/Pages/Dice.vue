<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';

const isRolling = ref(false);
const result = ref(null);
const face = ref(1);
let faceTimer;
let finishTimer;
const pips = {
    1: [5], 2: [1, 9], 3: [1, 5, 9],
    4: [1, 3, 7, 9], 5: [1, 3, 5, 7, 9], 6: [1, 3, 4, 6, 7, 9],
};
const status = computed(() => isRolling.value
    ? 'ダイスを転がしています…'
    : result.value === null ? 'ボタンを押して、運試し。' : `結果は ${result.value} です！`);

function roll() {
    if (isRolling.value) return;
    isRolling.value = true;
    result.value = null;
    const nextResult = Math.floor(Math.random() * 6) + 1;
    faceTimer = window.setInterval(() => {
        face.value = face.value % 6 + 1;
    }, 100);
    finishTimer = window.setTimeout(() => {
        window.clearInterval(faceTimer);
        face.value = nextResult;
        result.value = nextResult;
        isRolling.value = false;
    }, 2400);
}

onBeforeUnmount(() => {
    window.clearInterval(faceTimer);
    window.clearTimeout(finishTimer);
});
</script>

<template>
    <Head title="数字合わせダイス" />
    <div class="dice-page">
        <header class="site-header">
            <Link href="/" class="brand">IRIAM Support</Link>
            <span class="public-label">ログイン不要</span>
        </header>
        <main class="dice-main">
            <p class="eyebrow">配信のおともに</p>
            <h1>数字合わせダイス</h1>
            <p class="description">次はどの数字？ 1〜6のダイスを振ってみよう。</p>
            <section class="dice-card" aria-label="ダイス">
                <div class="stage" :aria-busy="isRolling">
                    <div class="die" :class="{ rolling: isRolling }" aria-hidden="true">
                        <span v-for="position in 9" :key="position" class="pip"
                            :class="{ visible: pips[face].includes(position) }" />
                    </div>
                </div>
                <p class="result" role="status" aria-live="polite" aria-atomic="true">{{ status }}</p>
                <button type="button" class="roll-button" :disabled="isRolling" @click="roll">
                    {{ isRolling ? '転がっています…' : result === null ? 'ダイスを振る' : 'もう一度振る' }}
                </button>
                <p class="hint">約2〜3秒で結果が出ます</p>
            </section>
            <p class="footnote">リスナーさんとの数字合わせや、ちょっとした運試しに。</p>
        </main>
    </div>
</template>

<style scoped>
.dice-page { min-height: 100svh; background: #f4f7fc; color: #20334a; }
.site-header { max-width: 1080px; margin: auto; padding: 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.brand { font-weight: 800; text-decoration: none; color: #245a9b; }
.public-label { font-size: 12px; background: #e4eefb; padding: 6px 12px; border-radius: 20px; white-space: nowrap; }
.dice-main { max-width: 620px; margin: auto; padding: 36px 20px 48px; text-align: center; }
.eyebrow { color: #4774a8; font-size: 13px; font-weight: 700; letter-spacing: .15em; }
h1 { font-size: clamp(26px, 6vw, 36px); font-weight: 800; margin: 10px 0 14px; }
.description, .footnote { font-size: 14px; line-height: 1.9; color: #617187; }
.dice-card { margin: 28px 0 20px; background: white; border: 1px solid #e1e8f2; border-radius: 28px; padding: 24px; box-shadow: 0 16px 48px #2349760a; }
.stage { height: 220px; display: grid; place-items: center; overflow: hidden; background: radial-gradient(ellipse, #eaf2ff, #fff 70%); border-radius: 18px; }
.die { width: 96px; height: 96px; display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(3, 1fr); gap: 8px; padding: 16px; background: white; border: 2px solid #d4dfef; border-radius: 21px; box-shadow: 0 8px 0 #d8e3f1, 0 16px 24px #31588120; }
.pip { border-radius: 50%; background: #2d63a5; visibility: hidden; }
.pip.visible { visibility: visible; }
.rolling { animation: tumble 2400ms ease-out; }
.result { min-height: 36px; margin: 16px 0; font-size: 22px; font-weight: 700; }
.roll-button { width: 100%; min-height: 56px; padding: 14px 20px; border-radius: 14px; background: #2863ad; color: white; font-size: 17px; font-weight: 700; touch-action: manipulation; }
.roll-button:hover:enabled { background: #204f8b; }
.roll-button:focus-visible, .brand:focus-visible { outline: 3px solid #e9a628; outline-offset: 4px; }
.roll-button:disabled { opacity: .6; cursor: wait; }
.hint { margin-top: 12px; font-size: 12px; color: #6b7c92; }
@keyframes tumble {
    0% { transform: translate(-70px, -24px) rotate(-180deg); }
    20% { transform: translate(65px, 16px) rotate(100deg); }
    40% { transform: translate(-45px, -28px) rotate(260deg); }
    60% { transform: translate(30px, 10px) rotate(320deg); }
    80% { transform: translate(-12px, -8px) rotate(350deg); }
    100% { transform: translate(0, 0) rotate(360deg); }
}
@media (max-width: 380px) {
    .site-header { padding: 20px 16px; }
    .dice-main { padding: 20px 12px 32px; }
    .dice-card { padding: 18px; }
    .result { font-size: 19px; }
}
@media (prefers-reduced-motion: reduce) {
    .rolling { animation: none; }
}
</style>
