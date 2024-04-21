<template>
  <div>
    <img :src="gravatarUrl" :alt="altText" :width="size" :height="size" class="rounded-md" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import md5 from 'md5';

const props = defineProps({
  email: {
    type: String,
    required: true
  },
  size: {
    type: Number,
    default: 80
  },
  altText: {
    type: String,
    default: 'Gravatar Image'
  }
});

const gravatarUrl = computed(() => {
  const emailHash = calculateEmailHash(props.email);
  console.debug(props.email)
  return `https://2.gravatar.com/avatar/${emailHash}?s=${props.size}&d=mp`;
});


function calculateEmailHash(email: string): string {
  const trimmedEmail = email.trim().toLowerCase();
  const emailHash = md5(trimmedEmail);
  return emailHash;
}
</script>
