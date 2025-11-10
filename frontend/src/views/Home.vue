<template>
  <div class="p-6 space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- <StatsCard 
        title="Actieve Oproepen" 
        :value="dashboardStore.stats.activeCalls"
        change="+12%"
        trend="up"
        icon='<svg fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>'
        icon-bg="bg-blue-100"
        icon-color="text-blue-600" />
      
      <StatsCard 
        title="Totaal WholeSales" 
        :value="wholesaleStore.wholesalesCount"
        change="+8%"
        trend="up"
        icon='<svg fill="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
        icon-bg="bg-green-100"
        icon-color="text-green-600" />
      
      <StatsCard 
        title="Totaal Customers" 
        :value="customerStore.customersCount"
        change="+23%"
        trend="up"
        icon='<svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/></svg>'
        icon-bg="bg-yellow-100"
        icon-color="text-yellow-600" />

      <StatsCard 
        title="Totaal Resellers" 
        :value="resellerStore.resellersCount"
        change="+23%"
        trend="up"
        icon='<svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/></svg>'
        icon-bg="bg-yellow-100"
        icon-color="text-yellow-600" /> -->
      
      <!-- <StatsCard 
        title="Maandelijkse Omzet" 
        :value="'€' + dashboardStore.stats.monthlyRevenue.toLocaleString()"
        change="+23%"
        trend="up"
        icon='<svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/></svg>'
        icon-bg="bg-yellow-100"
        icon-color="text-yellow-600" />
      
      <StatsCard 
        title="Server Uptime" 
        :value="dashboardStore.stats.serverUptime + '%'"
        change="+0.2%"
        trend="up"
        icon='<svg fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/></svg>'
        icon-bg="bg-purple-100"
        icon-color="text-purple-600" /> -->
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- <CallVolumeChart />
      <RevenueChart /> -->
    </div>
    
    <!-- Recent Activity -->
    <!-- <CallsTable :calls="dashboardStore.recentCalls" /> -->
     Halloooo
  </div>
</template>

<script setup>
//import { onMounted, ref } from 'vue';

//Import van stores
import { useDashboardStore } from '../stores/dashboard'

const dashboardStore = useDashboardStore()

</script>