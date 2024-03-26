<script setup lang="ts">

const topics = [
    {
        title: "General",
        description: "Lorem Ipsum Potato",
        color: "border-green-500",
        threadCount: 581,
        messageCount: 6100
    },
    {
        title: "Projects",
        description: "All of your projects",
        color: "border-blue-500",
        threadCount: 6,
        messageCount: 158
    },
    {
        title: "Security",
        description: "Hello Idegen",
        color: "border-red-500",
        threadCount: 61,
        messageCount: 252
    },
    {
        title: "Off-Topic",
        description: "Minden, ami bableves",
        color: "border-gray-500",
        threadCount: 6127,
        messageCount: 12
    }
];
</script>
<template>
    <div id="topics" class="flex flex-col w-full gap-8 xl:flex-row">
        <div class="flex-1 mt-12 space-y-4">
            <div v-for="(topic, index) in topics" :key="index" :class="`${topic.color} border-l-8 bg-gray-800 rounded-xl flex flex-row justify-between items-center p-8`">
                <div class="flex items-center gap-4 ">
                    <div class="flex flex-col">
                        <div id="title">{{ topic.title }}</div>
                        <div id="description" class="">
                            <p class="text-gray-500 ">{{ topic.description }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-12">
                    <div class="flex flex-col text-left">
                        <span class="text-lg font-medium text-white">{{ topic.threadCount }}</span>
                        <span class="text-gray-500"> Threads</span>
                    </div>
                    <div class="flex flex-col text-left">
                        <span class="text-lg font-medium text-white">{{ topic.messageCount }}</span>
                        <span class="text-gray-500"> Messages</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
