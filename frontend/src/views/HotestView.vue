<script setup lang="ts">
import HotView from './topics/HotView.vue';

</script>

<template>
    <div class="flex flex-col w-full gap-8 xl:flex-row">
        <div class="flex-1">
            <div class="flex flex-col gap-4 xl:flex-row">
                <HotView class="flex-1"></HotView>
            </div>
        </div>
    </div>
</template>