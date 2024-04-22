<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import moment from 'moment';
import { useAuthStore } from '@/stores/auth.store';
import { useRouter } from 'vue-router';
import { EnvelopeIcon } from '@heroicons/vue/24/solid';

interface Threads {
    id: number;
    name: string;
    description: string;
    created_at: Date;
    user_id: number;
    username: string;
    comment_count: number;

    // Add any other properties you have for comments
}

const auth = useAuthStore();
const router = useRouter();

const threads = ref<Threads[]>([]);
const token = computed(() => auth.getToken());
const baseURL = import.meta.env.VITE_APP_URL;

const fetchComments = async () => {
    try {
        await auth.checkAuth();
        const response = await fetch(`${import.meta.env.VITE_API_URL}/topics`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token.value}`
            }
        });
        if (response.ok) {
            const responseData = await response.json();
            const messagesNonSorted: Threads[] = responseData.result;
            threads.value = messagesNonSorted.sort((a, b) => {
                const timeA = new Date(a.created_at);
                const timeB = new Date(b.created_at);
                return timeB.getTime() - timeA.getTime();
            });

        } else if (response.status === 401) {
            router.push('/login');
        }
    } catch (error) {
        console.error('Error fetching comments:', error);
    }
};


onMounted(() => {
    fetchComments();
});

const getRelativeTime = (time: Date) => {
    return moment(time).fromNow();
}


</script>


<template>
    <div id="latests" class="flex flex-col w-full gap-8 xl:flex-row">
        <div class="flex-1 mt-12 space-y-4">
            <div v-for="(message, index) in threads" :key="index"
                :class="`bg-gray-800 border-primary border-l-8 rounded-xl flex flex-row justify-between items-center p-5`">
                <div class="flex items-center gap-4">

                    <a :href="`${baseURL + '/profile/' + message.user_id}`">
                       <EnvelopeIcon class="w-auto mt-1 h-9"></EnvelopeIcon>
                    </a>
                    <div class="flex flex-col">
                        <a id="title" :href="`${baseURL + '/discussions/thread/' + message.id}`">{{ message.name
                            }}</a>
                         
                            <p class="text-xs text-gray-500">{{ message.description }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-12">
                    <div class="flex flex-col gap-1 text-right">
                        <span class="text-lg font-semibold text-white">{{ message.comment_count }}</span>
                        <span class="text-sm text-gray-500">{{ getRelativeTime(message.created_at) }}</span>
                        <a class="flex items-center justify-end text-xs text-right text-primary/75"
                            :href="`${baseURL + '/profile/' + message.user_id}`">
                            <UserIcon class="w-3.5"></UserIcon> {{ message.username }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
