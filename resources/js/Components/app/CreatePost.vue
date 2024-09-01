<script setup>

import {ref} from "vue";
import PostModal from "@/Components/app/PostModal.vue";
import {usePage} from "@inertiajs/vue3";

const authUser = usePage().props.auth.user

const showModal = ref(false)
const newPost = ref({
    id: null,
    body: '',
    user: authUser
})


function showCreatePostModal() {
    showModal.value = true
}


defineProps({
    group: {
        type: Object,
        default: null
    }
})

</script>

<template>
    <div class="p-4  bg-white rounded-lg  border mb-3">
        <div
            class="py-2 px-3 border-2 border-gray-200 text-gray-500 rounded-md  mb-3 w-full"
            @click="showCreatePostModal">Click here to create new post
        </div>
        <PostModal v-model="showModal" :group="group" :post="newPost"/>

    </div>
</template>

<style scoped>

</style>
