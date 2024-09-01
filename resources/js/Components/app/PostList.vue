<script setup>

import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "@/Components/app/PostModal.vue";
import {onMounted, ref, watch} from "vue";
import AttachmentPreviewModal from "@/Components/app/AttachmentPreviewModal.vue";
import instance from "@/axiosClient.js";
import {usePage} from "@inertiajs/vue3";

const page = usePage()


const authUser = usePage().props.auth.user
const showEditModal = ref(false);
const showAttachmentsModal = ref(false);
const editPost = ref({});
const previewAttachmentsPost = ref({});
const loadMoreIntersect = ref(null);

const allPosts = ref({
    data: [],
    next: null,
})

const props = defineProps({
    posts: Array,
})

watch(() => page.props.posts, () => {
    if (page.props.posts) {
        allPosts.value = {
            data: page.props.posts.data,
            next: page.props.posts.links?.next,
        }
    }
}, {deep: true, immediate: true})

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

function loadMore() {
    if (!allPosts.value.next) {
        return;
    }
    instance.get(allPosts.value.next)
        .then(({data}) => {
            allPosts.value.data = [...allPosts.value.data, ...data.data]
            allPosts.value.next = data.links.next
        })
}

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
            rootMargin: '-250px 0px 0px 0px'
        })

    observer.observe(loadMoreIntersect.value)
})
</script>

<template>
    <div class="overflow-auto">
        <PostItem v-for="post of allPosts.data" :key="post.id" :post="post"
                  @attachmentClick="openAttachmentPreviewModal"
                  @editClick="openEditModal"
        />
        <div ref="loadMoreIntersect"></div>
        <PostModal v-model="showEditModal" :post="editPost" @hide="onModalHide"/>

        <AttachmentPreviewModal v-model="showAttachmentsModal"
                                v-model:index="previewAttachmentsPost.index"
                                :attachments="previewAttachmentsPost.post?.attachments || []"
        />
    </div>

</template>

<style scoped>

</style>
