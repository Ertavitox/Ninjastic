<template>
    <div class="flex flex-col max-w-7xl">
        <div class="max-w-xs p-3 pl-5 bg-gray-800 rounded-t-3xl">Posting as <a
                class="transition-all text-secondary-500 hover:cursor-pointer hover:text-secondary-300"
                @click.prevent>@{{ username }}</a></div>
        <div class="flex flex-col gap-5 lg:flex-row">
            <form class="flex flex-1">
                <textarea v-model="comment" placeholder="Type a comment"
                    class="items-start justify-start flex-1 w-full p-5 pb-12 text-left text-white bg-gray-700 rounded-b-lg rounded-tr-lg focus:ring-primary max-w-7xl focus:ring focus:outline-none"></textarea>
            </form>
            <div class="grid min-w-lg">
                <div v-for="row in smileyRows" :key="row" class="flex justify-start gap-2">
                    <div v-for="smiley in smileys[row]" :key="smiley" @click="insertSmiley(smiley)">
                        {{ smiley }}
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-lg my-4 ">
            <button :disabled="commentEmpty" class="px-12 py-2 rounded-lg bg-secondary-500 disabled:bg-gray-700"
                @click="postComment()">Submit </button>
        </div>
        <div v-if="errorMessage">{{ errorMessage }}</div>
    </div>
</template>

<script>
import { defineComponent, computed } from 'vue';
export default defineComponent({
    data() {
        return {
            username: "Marcell Csendes",
            comment: '',
            errorMessage: '',
            comments: [],
            apiUrl: import.meta.env.VITE_API_URL,
            topicId: 1,
            token: computed(() => {
                const user = localStorage.getItem('user');
                return user ? JSON.parse(user).token : '';
            }),
            smileys: [
                ['😀', '😁', '😂', '😃', '😄', '😅'],
                ['😆', '😇', '😈', '😉', '😊', '😋'],
                ['😌', '😍', '😎', '😏', '😐', '😑'],
                ['😒', '😓', '😔', '😕', '😖', '😗']
            ]
        };
    },
    computed: {
        smileyRows() {
            return Object.keys(this.smileys);
        },
        commentEmpty() {
            return this.comment.trim() === '';
        }
    },
    methods: {
        insertSmiley(smiley) {
            this.comment += smiley;
        },
        async postComment() {
            try {
                await fetch(`${this.apiUrl}/topic/${this.topicId}/comments/`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${this.token}`
                    },
                    body: JSON.stringify(
                        {
                            "original": this.comment
                        }
                    ),
                })
                this.fetchComments();
            } catch (err) {
                console.error(err);
            }
        },
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
                    const commentsNonSorted = responseData.result;
                    this.comments = commentsNonSorted.sort((b, a) => a.id - b.id)

                } else if (response.status === 401) {
                    router.push('/login')
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    }
});
</script>

<style scoped></style>