<script setup lang="ts">
import TopicView from './topics/TopicView.vue';
import LatestView from './topics/LatestView.vue';
import StaffOnline from '../components/StaffOnline.vue';
import { storeToRefs } from 'pinia';

import { useAuthStore } from '@/stores/auth.store';

const authStore = useAuthStore();
const { user: authUser } = storeToRefs(authStore);
</script>

<template>
    <div class="flex flex-col w-full gap-8 xl:flex-row">
        <div class="flex-1">
            <div class="flex flex-col gap-4 xl:flex-row">
             
                <TopicView class="flex-1"></TopicView>
                <LatestView class="flex-1"></LatestView>
            </div>
          
        </div>
        <StaffOnline class="xl:mx-12"></StaffOnline>
    </div>
</template>