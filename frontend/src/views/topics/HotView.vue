<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import moment from 'moment';
import { useAuthStore } from '@/stores/auth.store';
import { useRouter } from 'vue-router';
import GravatarImage from '@/components/GravatarImage.vue';
import { UserIcon } from '@heroicons/vue/24/solid';
interface Threads {
    id: number;
    name: string;
    description: string;
    created_at: Date;
    user_id: number;
    username: string;
    comment_count: number;
}

const auth = useAuthStore();
const router = useRouter();
const currentPage = ref(1);
const limit = ref(10);
const threads = ref<Threads[]>([]);
const token = computed(() => auth.getToken());
const baseURL = import.meta.env.VITE_APP_URL;
let isLoading = false;

const isScrollAtBottom = () => {
    return window.innerHeight + window.scrollY >= document.body.offsetHeight;
};

const fetchThreadsOnScroll = async () => {
    try {
        if (isScrollAtBottom() && !isLoading) {
            isLoading = true;
            await fetchMoreThreads();
            isLoading = false;
        }
    } catch (error) {
        console.error('Error fetching threads:', error);
        isLoading = false;
    }
};

window.addEventListener('scroll', fetchThreadsOnScroll);

const fetchComments = async (page: number) => {
    try {
        await auth.checkAuth();
        const response = await fetch(`${import.meta.env.VITE_API_URL}/topics/hot/?page=${page}&limit=${limit.value}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token.value}`
            }
        });
        if (response.ok) {
            const responseData = await response.json();
            const newThreads: Threads[] = responseData.result;

            if (newThreads.length > 0) {
                threads.value = threads.value.concat(newThreads);
                currentPage.value++;
            } else {
                console.log('No more threads to fetch');
                window.removeEventListener('scroll', fetchThreadsOnScroll);
            }
        } else if (response.status === 401) {
            router.push('/login');
        }
    } catch (error) {
        console.error('Error fetching comments:', error);
    }
};

const fetchMoreThreads = async () => {
    currentPage.value++;
    await fetchComments(currentPage.value);
};

onMounted(() => {
    fetchComments(currentPage.value);
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
                        <GravatarImage :email="message.username ?? ''" class="w-12 h-auto"></GravatarImage>
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
