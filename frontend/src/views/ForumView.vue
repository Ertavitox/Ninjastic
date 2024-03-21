<script setup lang="ts">
import TopicView from './topics/TopicView.vue';
import LatestView from './topics/LatestView.vue';
import StaffOnline from '../components/StaffOnline.vue';
</script>

<template>
    <div class="flex flex-col gap-8 xl:flex-row w-full">
        <div class="flex-1">
            <div class="flex flex-col xl:flex-row gap-4">
                <TopicView class="flex-1"></TopicView>
            <LatestView class="flex-1"></LatestView>
            </div>
          
        </div>
        <StaffOnline class="xl:mx-12"></StaffOnline>
    </div>
</template>