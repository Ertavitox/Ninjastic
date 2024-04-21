<template>
  <div class="flex flex-col max-w-7xl">
    <div class="max-w-xs p-3 pl-5 bg-gray-800 rounded-t-3xl">
      Posting as <a :href="`${baseURL + '/profile/' + auth.userData?.id}`"
        class="transition-all text-secondary-500 hover:cursor-pointer hover:text-secondary-300" @click.prevent>@{{
        auth.userData?.name }}</a>
    </div>
    <div class="flex flex-col gap-5 lg:flex-row">
      <form class="flex flex-1">
        <textarea v-model="comment" placeholder="Type a comment"
          class="items-start justify-start flex-1 w-full p-5 pb-12 text-left text-white bg-gray-700 rounded-b-lg rounded-tr-lg focus:ring-primary max-w-7xl focus:ring focus:outline-none"></textarea>
      </form>
      <div class="grid min-w-lg">
        <div v-for="(row, rowIndex) in smileyRows" :key="rowIndex" class="flex justify-start gap-2">
          <div v-for="(smiley, index) in smileys[row]" :key="index" @click="insertSmiley(smiley)">
            {{ smiley }}
          </div>
        </div>
      </div>
    </div>
    <div class="max-w-lg my-4 ">
      <button :disabled="commentEmpty" class="px-12 py-2 rounded-lg bg-secondary-500 disabled:bg-gray-700"
        @click="postComment()">Submit</button>
    </div>
    <div v-if="errorMessage">{{ errorMessage }}</div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth.store';
import router from '@/router';
import { ref, computed, inject } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

interface Smileys {
  [key: string]: string[];
}


interface FetchCommentsFunction {
  (): Promise<void>;
}

const fetchComments = inject<FetchCommentsFunction>('fetchComments');
const comment = ref('');
const errorMessage = ref('');
const apiUrl = import.meta.env.VITE_API_URL;
const baseURL = import.meta.env.VITE_APP_URL;
const threadId = router.currentRoute.value.params.id;
const auth = useAuthStore();
const token = computed(() => auth.getToken());

const smileys: Smileys = {
  '1': ['😀', '😁', '😂', '😃', '😄', '😅'],
  '2': ['😆', '😇', '😈', '😉', '😊', '😋'],
  '3': ['😌', '😍', '😎', '😏', '😐', '😑'],
  '4': ['😒', '😓', '😔', '😕', '😖', '😗']
};

const smileyRows = Object.keys(smileys);

const commentEmpty = computed(() => comment.value.trim() === '');

const insertSmiley = (smiley: string) => {
  comment.value += smiley;
};

const postComment = async () => {
  try {
    const commentWithEscapedEmoji = (comment.value as string) ?
      (comment.value as string).replace(/[\u{1F600}-\u{1F64F}]/gu, (match: string | undefined) => {
        if (match) {
          return `\\u{${match.codePointAt(0)!.toString(16)}}`;
        }
        return '';
      }) :
      '';

    const response = await fetch(`${apiUrl}/topics/${threadId}/comments`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token.value}`
      },
      body: JSON.stringify({ original: commentWithEscapedEmoji })
    });

    if (response.ok) {
      // Parse the JSON response body
      const responseData = await response.json();
      toast.success(responseData.message, {
        position: toast.POSITION.BOTTOM_CENTER,
        theme: 'dark'
      });
      if (fetchComments) {
        await fetchComments();
      } else {
        console.error('Something went wrong');
      }
    } else {
      toast.error("Failed to post comment", {
        position: toast.POSITION.BOTTOM_CENTER
      });
    }
  } catch (err) {
    console.error(err);
  }
};

</script>