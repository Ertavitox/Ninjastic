<script setup lang="ts">
import LatestView from './topics/LatestView.vue';

</script>

<template>
    <div class="flex flex-col w-full gap-8 xl:flex-row">
        <div class="flex-1">
            <div class="flex flex-col gap-4 xl:flex-row">
                <LatestView class="flex-1"></LatestView>
            </div>
        </div>
    </div>
</template>