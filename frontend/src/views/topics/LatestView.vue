<script setup lang="ts">
import StaffOnline from '../../components/StaffOnline.vue';
import { NewspaperIcon, UserIcon } from '@heroicons/vue/24/solid';
import { WrenchScrewdriverIcon } from '@heroicons/vue/24/outline';
const baseURL = import.meta.env.VITE_APP_URL
const messages = [
    {
        id: 1,
        title: "BitNinja is Best",
        description: "Security",
        color: "bg-gray-800",
        iconColor: "bg-white",
        iconClass: "text-black",
        icon: UserIcon,
        count: 0,
        timeAgo: "6 Seconds Ago"
    },
    {   
        id: 2,
        title: "Pálinka szerelmem légy az enyém",
        description: "Off-Topic",
        color: "bg-gray-800",
        iconColor: "bg-red-500",
        iconClass: "",
        icon: UserIcon,
        count: 3,
        timeAgo: "12 Minutes Ago"
    },
    {
        id: 3,
        title: "My First Thread",
        description: "General",
        color: "bg-gray-800",
        iconColor: "bg-blue-700",
        iconClass: "",
        icon: UserIcon,
        count: 71,
        timeAgo: "4 Days Ago"
    }
];
</script>

<template>
    <div id="latests" class="flex flex-col w-full gap-8 xl:flex-row">
        <div class="flex-1 mt-12 space-y-4">
            <div v-for="(message, index) in messages" :key="index" :class="`${message.color} rounded-xl flex flex-row justify-between items-center p-5`">
                <div class="flex items-center gap-4">
                    <component :is="message.icon" :class="`h-auto w-10 p-2 ${message.iconColor} ${message.iconClass} rounded-full`" />
                    <div class="flex flex-col">
                        <a id="title" :href="`${ baseURL + '/discussions/thread/' + message.id }`">{{ message.title }}</a>
                        <div id="description">
                            <p class="text-gray-500">{{ message.description }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-12">
                    <div class="flex flex-col text-right">
                        <span class="text-lg font-semibold text-white">{{ message.count }}</span>
                        <span class="text-sm text-gray-500">{{ message.timeAgo }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
