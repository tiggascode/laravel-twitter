<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import AttachmentPreviewModal from "@/Components/app/AttachmentPreviewModal.vue";
import PostModal from "@/Components/app/PostModal.vue";
import {usePage} from "@inertiajs/vue3";

defineProps({
    post: Object
})
const authUser = usePage().props.auth.user
const showEditModal = ref(false);
const showAttachmentsModal = ref(false);
const editPost = ref({});
const previewAttachmentsPost = ref({});

function openEditModal(post) {
    editPost.value = post;
    showEditModal.value = true;
}

function openAttachmentPreviewModal(post, index) {
    previewAttachmentsPost.value = {
        post,
        index
    }
    showAttachmentsModal.value = true;
}

function onModalHide() {
    editPost.value = {
        id: null,
        body: '',
        user: authUser
    }
}

</script>

<template>
    <AuthenticatedLayout>
        <div class="p-8 w-[600px] mx-auto h-full overflow-auto">
            <PostItem :post="post" @attachmentClick="openAttachmentPreviewModal" @editClick="openEditModal"/>
        </div>
        <PostModal v-model="showEditModal" :post="editPost" @hide="onModalHide"/>

        <AttachmentPreviewModal v-model="showAttachmentsModal"
                                v-model:index="previewAttachmentsPost.index"
                                :attachments="previewAttachmentsPost.post?.attachments || []"/>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
