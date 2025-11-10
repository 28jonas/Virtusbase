<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <div class="flex h-screen">
      <Sidebar />
      <div class="flex-1 flex flex-col overflow-hidden">
        <Header />
        <main class="flex-1 overflow-y-auto">
          <router-view />
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import Sidebar from './components/layout/Sidebar.vue'
import Header from './components/layout/Header.vue'
</script>

<style>
@import './styles/globals.css';

.glass-effect {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.dark .glass-effect {
  background: rgba(42, 45, 62, 0.8);
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>