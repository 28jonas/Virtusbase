<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <div class="flex h-screen">
      <!-- Mobile Overlay -->
      <div 
        v-if="sidebarOpen" 
        class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
        @click="sidebarOpen = false"
      ></div>
      
      <!-- Sidebar -->
      <div 
        class="fixed lg:static inset-y-0 left-0 z-50 transform transition-transform duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
      >
        <Sidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />
      </div>
      
      <!-- Main Content -->
      <div class="flex-1 flex flex-col overflow-hidden w-full lg:w-auto">
        <Header @toggle-sidebar="sidebarOpen = !sidebarOpen" />
        <main class="flex-1 overflow-y-auto">
          <router-view />
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import Sidebar from './Sidebar.vue'
import Header from './Header.vue'

const sidebarOpen = ref(false)

// Close sidebar on route change (mobile)
import { useRoute } from 'vue-router'
const route = useRoute()

const closeSidebarOnRouteChange = () => {
  if (window.innerWidth < 1024) {
    sidebarOpen.value = false
  }
}

// Close sidebar on escape key
const handleEscape = (e) => {
  if (e.key === 'Escape' && sidebarOpen.value) {
    sidebarOpen.value = false
  }
}

// Close sidebar on resize to desktop
const handleResize = () => {
  if (window.innerWidth >= 1024) {
    sidebarOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleEscape)
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleEscape)
  window.removeEventListener('resize', handleResize)
})
</script>