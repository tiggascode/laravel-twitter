<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import {ChatBubbleLeftRightIcon, HandThumbUpIcon} from '@heroicons/vue/20/solid'
import PostUserHeader from "@/Components/app/PostUserHeader.vue";
import {router} from '@inertiajs/vue3'
import instance from "@/axiosClient.js";
import ReadMoreReadLess from "@/Components/app/ReadMoreReadLess.vue";
import EditDeleteDropdown from "@/Components/app/EditDeleteDropdown.vue";
import PostAttachment from "@/Components/app/PostAttachment.vue";
import CommentList from "@/Components/app/CommentList.vue";
import {computed} from "vue";

const props = defineProps({
    post: Object

})


const emit = defineEmits(['editClick', 'attachmentClick'])

const postBody = computed(() => props.post.body.replace(
    /(#\w+)(?![^>]*<\/a>)/g,
    (match, group) => {
        const encodedGroup = encodeURIComponent(group)
        return `<a href="/search/${encodedGroup}" class="hashtag">${group}</a>`
    })
)

function openEditModal() {
    emit('editClick', props.post)
}

function deletePost() {
    if (window.confirm('Are you sure you want to delete this post?')) {
        router.delete(route('post.destroy', props.post), {
            preserveScroll: true,
        })
    }
}

function openAttachment(ind) {
    emit('attachmentClick', props.post, ind)
}

function sendReaction() {
    instance.post(route('post.reaction', props.post), {
        type: 'like'
    })
        .then(({data}) => {
            props.post.current_user_has_reaction = data.current_user_has_reaction
            props.post.num_of_reactions = data.num_of_reactions
        })
}


</script>

<template>
    <div class="bg-white border rounded p-4 mb-3">
        <div class="flex items-center justify-between  mb-3">
            <PostUserHeader :post="post"/>
            <EditDeleteDropdown :post="post" :user="post.user" @delete="deletePost" @edit="openEditModal"/>

        </div>
        <div class="mb-3">
            <ReadMoreReadLess :content="postBody"/>
        </div>
        <div :class="[
            post.attachments.length === 1 ? 'grid-cols-1 ' : 'grid-cols-2'
        ]" class="grid   gap-3 mb-3">
            <PostAttachment :attachments="post.attachments" @attachmentClick="openAttachment"/>
        </div>
        <Disclosure v-slot="{ open }">
            <!--            like and comment buttons-->
            <div class="flex gap-2 ">
                <button
                    :class="[
                    post.current_user_has_reaction ?
                    'bg-sky-100 hover:bg-sky-200' :
                    ' bg-gray-100 hover:bg-gray-200',

                ]"
                    class="text-gray-800 flex gap-1 items-center justify-center rounded-lg
                py-2 px-4 flex-1"
                    @click="sendReaction">
                    <HandThumbUpIcon class="w-5 h-5  "/>
                    <span class="mr-2">{{ post.num_of_reactions }}</span>
                    {{ post.current_user_has_reaction ? 'Unlike' : 'Like' }}

                </button>
                <DisclosureButton
                    class="text-gray-800 bg-gray-100 hover:bg-gray-200 flex gap-1 items-center justify-center rounded-lg
                py-2 px-4 flex-1">
                    <ChatBubbleLeftRightIcon class="w-5 h-5"/>
                    <span class="mr-2">{{ post.num_of_comments }}</span>
                    Comment

                </DisclosureButton>
            </div>
            <DisclosurePanel class="comment-list mt-3 max-h-[400px] overflow-auto">
                <CommentList :data="{comments: post.comments}" :parent-comment="post.comments" :post="post"/>
            </DisclosurePanel>
        </Disclosure>

    </div>

</template>


