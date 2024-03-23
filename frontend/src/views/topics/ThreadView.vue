<template>
    <div id="latests" class="flex flex-col w-full gap-5">
        <div class="flex flex-col w-full gap-8 p-6 bg-gray-800 lg:p-12 lg:items-center lg:flex-row rounded-xl max-w-7xl"
            v-for="comment in comments" :key="comment.id">
            <div class="flex flex-row ">
                <div class="flex">
                    <UserIcon class="w-auto h-12 p-2 bg-gray-500 rounded-full lg:mx-2 "></UserIcon>
                    <div class="flex flex-col">
                        <a class="pl-2 transition-all text-primary hover:text-primary-300" :href="`${ baseURL + '/profile/' + comment.user_id}`">Marcell</a>
                        <span class="pl-2 text-xs text-gray-500">Frontend Developer</span>
                    </div>
                </div>


            </div>
            <div class="max-w-xl">{{ comment.message }}</div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';
import { UserIcon } from '@heroicons/vue/24/solid';
interface Comments {
    id: number,
    user_id: number,
    message: string
}

export default defineComponent({
    components: {
        UserIcon
    },
    data() {
        return {
            email: '',
            password: '',
            errorMessage: '',
            topicId: 1,
            apiUrl: `${import.meta.env.VITE_API_URL}`,
            baseURL: `${import.meta.env.VITE_APP_URL}`,
            comments: [] as Comments[],
            token: computed(() => {
                const user = localStorage.getItem('user');
                return user ? JSON.parse(user).token : '';
            })
        };
    },
    mounted() {
        this.fetchComments(); // Fetch comments when the component is mounted
    },
    methods: {
        async fetchComments() {
            try {
                const response = await fetch(`${this.apiUrl}/topic/${this.topicId}/comments`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.token}`
                    }
                });

                if (response.ok) {
                    const responseData = await response.json();
                    this.comments = responseData.result;
                    console.log(this.comments)
                } else {
                    console.error('Something went wrong');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }
});
</script>