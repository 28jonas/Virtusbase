<template>
  <div class="grid grid-cols-12 gap-4">
    <!-- {{--left part--}} -->
    <div class="col-span-12 lg:col-span-2">
      <HealthMetrics />
    </div>

    <!-- {{-- Middle part --}} -->
    <div class="col-span-12 lg:col-span-7">
      <div>
        <StartWorkout />
      </div>

      <div class="my-4 grid lg:grid-cols-12 gap-4">
        <div class="col-span-6">
          <ActivityTracking />
        </div>

        <div class="col-span-6 space-y-4">
          <!-- <MealViewer /> -->
          <HealthGoals />
        </div>
      </div>
    </div>

    <!-- {{--Right part--}} -->
    <div class="col-span-12 lg:col-span-3">
      <div class="order-1 md:order-2">
        <ScheduleWorkout />
      </div>
    </div>
  </div>
</template>

<script setup>
import HealthMetrics from '../components/sport/HealthMetrics.vue'
import StartWorkout from '../components/sport/StartWorkout.vue'
import ActivityTracking from '../components/sport/ActivityTracking.vue'
import HealthGoals from '../components/sport/HealthGoals.vue'
import ScheduleWorkout from '../components/sport/ScheduleWorkout.vue'
</script>