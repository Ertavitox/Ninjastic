<template>
  <div id="latests" class="flex flex-col w-full gap-5">
    <div id="newComment">
      <NewComment></NewComment>
    </div>
    <template v-if="comments.length === 0">
      <p>No comments yet.</p>
    </template>
    <template v-else>
      <div
        class="relative flex flex-col w-full gap-8 p-6 bg-gray-800 lg:p-12 lg:items-center lg:flex-row rounded-xl max-w-7xl"
        v-for="comment in comments" :key="comment.id">
        <div class="flex flex-row justify-between">
          <div class="flex min-w-40">
            <UserIcon class="w-auto h-12 p-2 bg-gray-500 rounded-full lg:mx-2 "></UserIcon>
            <div class="flex flex-col">
              <a class="pl-2 transition-all text-primary hover:text-primary-300"
                :href="`${baseURL + '/profile/' + comment.user_id}`">
                {{ comment.user_name }}
              </a>
              <span class="pl-2 text-xs text-gray-500">User</span>
            </div>
          </div>

          <template v-if="comment.user_id === userId">
            <span class="absolute top-0 right-0 flex items-center justify-end gap-1 mx-4 my-4 text-right text-primary">
              <TrashIcon @click="deleteComment(comment.id)" class="w-auto h-4 rounded-full lg:mx-2"></TrashIcon>
              <div @click="openEditModal(comment.id)" class="flex items-center gap-1 text-secondary">
                <PencilIcon class="w-auto h-4 rounded-full "></PencilIcon> Edit
              </div>
            </span>
          </template>
          
          <Modal :isModalOpen="isModalOpen" @close="closeEditModal">
            <template #content>
              <div class="p-4">
                <textarea v-model="editedComment"
                  class="w-full h-40 p-2 text-white border border-gray-300 rounded-md resize-none bg-gray-950"></textarea>
                <button @click="saveEditedComment"
                  class="px-4 py-2 mt-4 text-white bg-blue-500 rounded-md">Save Comment</button>
              </div>
            </template>
          </Modal>

          <span
            class="absolute bottom-0 right-0 flex items-center justify-end gap-1 mx-4 my-4 text-right text-gray-500">
            {{ getRelativeTime(comment.created_at) }}
          </span>

        </div>
        <div class="flex max-w-3xl mb-12 text-justify text-gray-300 text-pretty">{{ comment.message }}</div>
      </div>
    </template>

  </div>
</template>

<script lang="ts">
import { defineComponent, computed, onMounted, ref, provide } from 'vue';
import moment from 'moment';
import { UserIcon } from '@heroicons/vue/24/solid';
import { PencilIcon } from '@heroicons/vue/24/outline';
import { TrashIcon } from '@heroicons/vue/24/solid';
import router from '@/router/index';
import Modal from '@/components/ModalComp.vue';
import NewComment from '@/components/topics/NewComment.vue';
import { useAuthStore } from '@/stores/auth.store';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

interface Comments {
  id: number;
  user_id: number;
  user_name: string;
  message: string;
  created_at: string;
  updated_at: string;
  thread_id: number;
  editing: boolean;
}

export default defineComponent({
  components: {
    UserIcon,
    PencilIcon,
    TrashIcon,
    Modal,
    NewComment,
  },
  setup() {
    const auth = useAuthStore();
    const comments = ref<Comments[]>([]);
    const token = computed(() => auth.getToken());
    const baseURL = import.meta.env.VITE_APP_URL;
    const threadId = router.currentRoute.value.params.id;
    const editedComment = ref('');
    const isModalOpen = ref(false);
    let editingCommentId: number | null = null;

    const fetchComments = async () => {
      try {
        await auth.checkAuth();
        if (!threadId) {
          console.error('Missing threadId');
          return;
        }
        const response = await fetch(`${import.meta.env.VITE_API_URL}/topics/${threadId}/comments`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token.value}`
          }
        });
        if (response.ok) {
          const responseData = await response.json();
          const commentsNonSorted: Comments[] = responseData.result;

          comments.value = commentsNonSorted
            .map(comment => ({
              ...comment,
              message: decodeUnicode(comment.message)
            }))
            .sort((a, b) => b.id - a.id);

          await commentsNonSorted.map(comment => comment.user_id);

        } else if (response.status === 401) {
          router.push('/login');
        }
      } catch (error) {
        console.error('Error fetching comments:', error);
      }
    };

    const toggleEditing = (comment: Comments) => {
      comment.editing = !comment.editing;
    };

    const closeEditModal = () => {
      isModalOpen.value = false;
      editedComment.value = ''; // Clear edited comment when closing modal
      editingCommentId = null;
    };

    const openEditModal = async (commentId: number) => {
      isModalOpen.value = true;
      editingCommentId = commentId;
      // Fetch comment details and populate editedComment
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/topics/${threadId}/comments/${commentId}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token.value}`
          }
        });
        if (!response.ok) {
          throw new Error(`Failed to fetch comment details for comment with ID ${commentId}`);
        }
        const commentData = await response.json();
        const comment = commentData.result;
        editedComment.value = comment.message;
      } catch (error) {
        console.error('Error fetching comment details:', error);
      }
    };

    const saveEditedComment = async () => {
      if (editingCommentId === null) return; // Safety check
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/topics/${threadId}/comments/${editingCommentId}`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token.value}`,
          },
          body: JSON.stringify({ original: editedComment.value }),
        });
        const responseData = await response.json();
        if (response.ok) {
          toast.success(responseData.message, {
            position: toast.POSITION.BOTTOM_CENTER,
            autoClose: 3000,
          });
          // Clear editedComment and close modal after saving
          editedComment.value = '';
          isModalOpen.value = false;
          editingCommentId = null;
          await fetchComments();
        } else {
          toast.error(responseData.message, {
            position: toast.POSITION.BOTTOM_CENTER,
            autoClose: 3000,
          });
        }
      } catch (error) {
        console.error('Error updating comment: ', error);
      }
    };

    const deleteComment = async (id: number) => {
      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/topics/${threadId}/comments/${id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token.value}`
          }
        });
        const responseData = await response.json();
        if (response.ok) {
          toast.success(responseData.message, {
            position: toast.POSITION.BOTTOM_CENTER,
            autoClose: 3000
          });
          await fetchComments();
        } else {
          toast.error(responseData.message, {
            position: toast.POSITION.BOTTOM_CENTER,
            autoClose: 3000
          })
        }
      } catch (error) {
        console.error('Something went wrong. Error: ', error);
      }
    };

    provide('fetchComments', fetchComments);

    // Mivel a szerver nem tudja ezt feldolgozni, így unicodeba dekódoljuk az emojikat...
    const decodeUnicode = (text: string): string => {
      return text.replace(/\\u\{([0-9a-fA-F]+)\}/gu, (match, code) => {
        return String.fromCodePoint(parseInt(code, 16));
      });
    };

    const getRelativeTime = (time: string) => {
      return moment(time).fromNow();
    }

    onMounted(() => {
      fetchComments();
    });

    const userData = auth.getUserData();
    const userId = userData?.user_id;

    return {
      token,
      threadId,
      comments,
      baseURL,
      userId,
      toggleEditing,
      deleteComment,
      getRelativeTime,
      editedComment,
      openEditModal,
      closeEditModal,
      saveEditedComment,
      isModalOpen
    };
  }
});
</script>
