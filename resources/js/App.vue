<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from './stores/auth';
import AppHeader from './components/AppHeader.vue';
import AppFooter from './components/AppFooter.vue';
import ChatWidget from './components/ChatWidget.vue';

const auth = useAuthStore();

onMounted(() => {
    auth.fetchSettings();
    if (!auth.loaded) auth.fetchUser();
});
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <AppHeader />
        <main class="flex-1">
            <router-view />
        </main>
        <AppFooter />
        <ChatWidget />
    </div>
</template>
