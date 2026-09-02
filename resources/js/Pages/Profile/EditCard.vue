<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    profile: Object,
});

const form = useForm({
    display_name: props.profile.display_name || '',
    oshi_mark: props.profile.oshi_mark || '',
    fan_name: props.profile.fan_name || '',
    bio: props.profile.bio || '',
    avatar_url: props.profile.avatar_url || '',
    theme_color: props.profile.theme_color || '#5A67D8',
    iriam_url: props.profile.iriam_url || '',
    twitter_id: props.profile.twitter_id || '',
    marshmallow_url: props.profile.marshmallow_url || '',
});

const submit = () => {
    form.put(route('card.update'));
};
</script>

<template>
    <Head title="Web名刺編集" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Web名刺・プロフィール編集</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- 表示名 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ライバー名 / 表示名 *</label>
                            <input v-model="form.display_name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <div v-if="form.errors.display_name" class="text-red-500 text-xs mt-1">{{ form.errors.display_name }}</div>
                        </div>

                        <!-- 推しマーク & ファンネーム -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">推しマーク</label>
                                <input v-model="form.oshi_mark" type="text" placeholder="例: 🤍✨" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ファンネーム</label>
                                <input v-model="form.fan_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <!-- テーマカラー -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">カードのテーマカラー</label>
                            <div class="flex items-center gap-3 mt-1">
                                <input v-model="form.theme_color" type="color" class="h-10 w-20 rounded border border-gray-300 cursor-pointer" />
                                <span class="text-sm font-mono text-gray-600">{{ form.theme_color }}</span>
                            </div>
                        </div>

                        <!-- 立ち絵・アイコンURL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">画像URL（立ち絵 / アイコン）</label>
                            <input v-model="form.avatar_url" type="url" placeholder="https://..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <!-- 各種SNS・リンク -->
                        <div class="space-y-4 pt-2">
                            <h3 class="font-medium text-gray-900 border-b pb-2">リンク設定</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">IRIAM プロフィールURL</label>
                                <input v-model="form.iriam_url" type="url" placeholder="https://web.iriam.app/..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">X (Twitter) ID</label>
                                <div class="flex mt-1 rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">@</span>
                                    <input v-model="form.twitter_id" type="text" placeholder="username" class="flex-1 rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">マシュマロ / 質問箱 URL</label>
                                <input v-model="form.marshmallow_url" type="url" placeholder="https://marshmallow-qa.com/..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <!-- 自己紹介 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">自己紹介文</label>
                            <textarea v-model="form.bio" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <!-- 保存ボタン -->
                        <div class="flex items-center justify-end gap-4">
                            <span v-if="form.recentlySuccessful" class="text-sm text-green-600 font-medium">保存しました！</span>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50">
                                保存する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>